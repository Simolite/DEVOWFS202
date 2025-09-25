<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!in_array($_SESSION['role'], ['student', 'teacher', 'admin'])){
    header('Location: login.php');
}

$conn = new mysqli('localhost', 'root', '', 'school_app');
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

function getTerms($conn){
    $stmt = "SELECT * FROM terms";
    $result = $conn->query($stmt);
    $terms = [];
    while ($row = $result->fetch_assoc()) {
        $terms[] = $row;
    }
    return $terms;   
}

function getStudentSubjects($conn,$student_id){
    $stmt = "SELECT id, name FROM subjects WHERE id IN (SELECT subject_id FROM class_subject WHERE class_id = (SELECT class_id FROM students WHERE id = $student_id))";
    $result = $conn->query($stmt);
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    return $subjects;
}

function getStudentMarks($conn,$student_id,$subjects,$term){
    $marks = [];
    foreach($subjects as $subject){
        $subject_id = $subject['id'];
        $sql = "SELECT * FROM marks WHERE student_id = $student_id AND subject_id = $subject_id AND `term_id` = $term ";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $marks[$subject['name']][] = $row;
        }
    }
    return $marks;
}

function getTeacherSubjects($conn,$teacher_id){
    $subs = [];
    $sql = "SELECT id , name FROM subject_teacher st INNER JOIN subjects s ON st.subject_id = id WHERE st.teacher_id = $teacher_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $subs[]= $row;
    }
    return $subs;
}

function getTeacherClasses($conn,$teacher_id){
    $classes = [];
    $sql = "SELECT id, name FROM class_teacher ct INNER JOIN classes c ON ct.class_id = id WHERE ct.teacher_id = $teacher_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $classes[]= $row;
    }
    return $classes;
}

function getClassStudents($conn,$class_id){
    $students = [];
    $sql = "SELECT * FROM students WHERE class_id = $class_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $students[] = $row;
    }
    return $students;
}

function addMark($conn,$student_id,$subject_id,$mark,$term_id,$date){
    $sql = "INSERT INTO marks (student_id, subject_id, mark, exam_date, term_id) VALUES ($student_id, $subject_id, $mark, '$date', $term_id)";
    $conn->query($sql);
}

function getAnnouncements($conn,$audience,$class_id=0){
    $announcements = [];
    if($class_id==0){
        $sql = "SELECT * FROM announcements WHERE audience = '$audience'";
    }elseif($class_id=='all'){
        $sql = "SELECT * FROM announcements";
    }else{
        $sql = "SELECT * FROM announcements WHERE audience = '$audience' OR class_id = $class_id";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $announcements[] = $row;
    } 
    return $announcements;
}

function getUserInfo($conn,$id){
    $student = [];
    $sql = "SELECT * FROM students WHERE id = '$id'";
    $result = $conn->query($sql);
    $student = $result->fetch_assoc();
    return $student;
}

function addAtt($conn,$student_id,$subject_id,$stat,$date){
    $sql = "INSERT INTO attendance (student_id, subject_id, status, date) VALUES ($student_id, $subject_id, '$stat', '$date')";
    $conn->query($sql);
}

function getAttendanceInfo($conn,$student_id){
    $att = [];
    $sql = "SELECT a.id AS attendance_id, s.name AS subject_name, a.date AS date FROM attendance a JOIN subjects s ON a.subject_id = s.id WHERE a.student_id = $student_id AND a.status = 'absent'ORDER BY a.date DESC;
";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $att[] = $row;
    } 
    return $att;
}

function getReports($conn,$student_id){
    $reports = [];
    $sql = "SELECT t.name AS term_name,t.start_date,t.end_date,r.average_score,r.rank,r.comments,r.url FROM report_cards r JOIN terms t ON r.term_id = t.id WHERE r.student_id = $student_id;";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $reports[] = $row;
    } 
    return $reports;
}

function getAccount($conn,$role, $user_id) {
    if ($user_id == 0) {
        $accounts = [];
        $sql = "SELECT * FROM users WHERE role = '$role'";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $accounts[] = $row;
            }
        }
        return $accounts;
    } else {
        $sql = "SELECT * FROM users WHERE role = '$role' AND linked_id = $user_id";
        $result = $conn->query($sql);
        $accounts = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $accounts[] = $row;
            }
        }
        return $accounts;
    }
}

function getStudentTeachers($conn,$student_id){
    $sql = "
        SELECT DISTINCT *
        FROM teachers
        JOIN subjects ON teachers.id = subjects.id
        JOIN student_subject ON subjects.id = student_subject.subject_id
        WHERE student_subject.student_id = $student_id
    ";

    $teachers = [];
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
    }

    return $teachers;
}

function sendMessages ($conn,$reciver_id,$reciver_role,$message,$title,$type,$date,$sender_id,$sender_role){
    $sql = "INSERT INTO `messages`(`sender_id`, `receiver_id`, `message`, `sent_at`, `sender_role`, `receiver_role`, `title`, `type`) VALUES ('$sender_id','$reciver_id','$message','$date','$sender_role','$reciver_role','$title','$type')";
    $result = $conn->query($sql);
    return $result;
}

function getMessages($conn,$id,$role){
    $sql = "SELECT 
    m.*, 
    CASE 
        WHEN m.sender_role = 'student' THEN CONCAT(s.fname, ' ', s.lname)
        WHEN m.sender_role = 'teacher' THEN CONCAT(t.fname, ' ', t.lname)
        WHEN m.sender_role = 'admin' THEN CONCAT(a.fname, ' ', a.lname)
    END AS sender_name
FROM messages m
LEFT JOIN students s ON m.sender_role = 'student' AND m.sender_id = s.id
LEFT JOIN teachers t ON m.sender_role = 'teacher' AND m.sender_id = t.id
LEFT JOIN admins a ON m.sender_role = 'admin' AND m.sender_id = a.id
WHERE m.receiver_role = '$role' AND m.receiver_id = $id;";
    $result = $conn->query($sql);
    $messages = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    return $messages;
}

function dellAnnouncement($conn,$id){
    $sql = "DELETE FROM announcements WHERE `announcements`.`id` = $id";
    $conn->query($sql);
}

function addAnnouncement($conn, $title, $body, $date, $audience, $id) {
    $sql = "INSERT INTO announcements (title, body, created_at, audience, class_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $body, $date, $audience, $id);
    return $stmt->execute();
}

function getClasses($conn){
    $sql ="SELECT * FROM classes";
    $result = $conn->query($sql);
    $classes = [];
    if($result){
        while($row = $result->fetch_assoc()){
            $classes[] = $row;
        };
    };
    return $classes;
}

function getTeachers($conn){
    $sql ="SELECT * FROM teachers";
    $result = $conn->query($sql);
    $teachers = [];
    if($result){
        while($row = $result->fetch_assoc()){
            $teachers[] = $row;
        };
    };
    return $teachers;
}

function changePassword($conn, $pass, $user_id) {
    $sql = "UPDATE `users` SET `password` = ? WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $pass, $user_id);

    return $stmt->execute();
}

function dellAttendance($conn,$id){
    $sql = "DELETE FROM attendance WHERE `attendance`.`id` = $id";
    $conn->query($sql);
}
?>
