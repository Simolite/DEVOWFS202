<?php

require_once 'conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!in_array($_SESSION['role'], ['student', 'teacher', 'admin'])){
    header('Location: login.php');
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
    if ($teacher_id == 'all'){
        $sql = "SELECT * FROM subjects ";
    }else {
        $sql = "SELECT id , name FROM subject_teacher st INNER JOIN subjects s ON st.subject_id = id WHERE st.teacher_id = $teacher_id";
    }
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
    $sql = "INSERT INTO marks (student_id, subject_id, mark, exam_date, term_id) VALUES ($student_id, $subject_id, '$mark', '$date', $term_id)";
    $conn->query($sql);
}

function getAnnouncements($conn,$id,$role){
    $announcements = [];
    if($role == 'admin'){
        $sql = "SELECT * FROM announcements WHERE 1";
    }else{
        $sql = "SELECT * FROM announcements WHERE audience = '$role' and linked_id = '$id' OR audience = 'all'";
    }
    
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $announcements[] = $row;
    } 
    return $announcements;
};

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

function sendMessages($conn, $receiver_id, $receiver_role, $message, $title, $type, $sender_id, $sender_role) {
    $sql = "INSERT INTO messages 
        (sender_id, receiver_id, message, sender_role, receiver_role, title, type)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "iisssss",
        $sender_id,
        $receiver_id,
        $message,
        $sender_role,
        $receiver_role,
        $title,
        $type
    );

    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
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
    $sql = "INSERT INTO announcements (title, body, created_at, audience, linked_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $body, $date, $audience, $id);
    return $stmt->execute();
}

function getClasses($conn){
    $sql ="SELECT classes.*, time_table.url AS timetable_url
FROM classes
LEFT JOIN time_table
    ON classes.timetable_id = time_table.id;
";
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

function dellMark($conn,$id){
    $sql = "DELETE FROM marks WHERE `marks`.`id` = $id";
    $conn->query($sql);
}


function createClass($conn, $className) {
    if (!$conn || $conn->connect_error) {
        error_log("Database connection error: " . ($conn->connect_error ?? 'Unknown'));
        return false;
    }
    
    $conn->begin_transaction();
    
    try {
        // Check if class name already exists
        $checkStmt = $conn->prepare("SELECT id FROM classes WHERE name = ?");
        $checkStmt->bind_param("s", $className);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $checkStmt->close();
            throw new Exception("Class name already exists");
        }
        $checkStmt->close();
        
        // Create timetable
        if (!$conn->query("INSERT INTO time_table (url) VALUES ('')")) {
            throw new Exception("Failed to create timetable: " . $conn->error);
        }
        $timetableId = $conn->insert_id;
        
        // Create class
        $classStmt = $conn->prepare("INSERT INTO classes (name, timetable_id) VALUES (?, ?)");
        $classStmt->bind_param("si", $className, $timetableId);
        
        if (!$classStmt->execute()) {
            throw new Exception("Failed to create class: " . $classStmt->error);
        }
        $classId = $conn->insert_id;
        $classStmt->close();
        
        // Insert all teachers for this class
        $teacherStmt = $conn->prepare("
            INSERT INTO class_teacher (class_id, teacher_id) 
            SELECT ? as class_id, id as teacher_id FROM teachers
        ");
        $teacherStmt->bind_param("i", $classId);
        
        if (!$teacherStmt->execute()) {
            throw new Exception("Failed to assign teachers to class: " . $teacherStmt->error);
        }
        $teacherStmt->close();
        
        // Insert all subjects for this class
        $subjectStmt = $conn->prepare("
            INSERT INTO class_subject (class_id, subject_id) 
            SELECT ? as class_id, id as subject_id FROM subjects
        ");
        $subjectStmt->bind_param("i", $classId);
        
        if (!$subjectStmt->execute()) {
            throw new Exception("Failed to assign subjects to class: " . $subjectStmt->error);
        }
        $subjectStmt->close();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Create class error: " . $e->getMessage());
        return false;
    }
}

function editClass($conn, $id, $name, $timetable_url) {
    // Validate inputs
    if (empty($id) || empty($name)) {
        error_log("Invalid input parameters for editClass");
        return false;
    }
    
    // Check if timetable_url is null, set to empty string if needed
    if ($timetable_url === null) {
        $timetable_url = '';
    }
    
    $sql = "UPDATE classes c
            JOIN time_table t ON c.timetable_id = t.id
            SET c.name = ?,
                t.url = ?
            WHERE c.id = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare statement failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("ssi", $name, $timetable_url, $id);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        
        $stmt->close();
        return $result;
        
    } catch (Exception $e) {
        error_log("editClass error: " . $e->getMessage());
        return false;
    }
}

function addStudent($conn, $fname, $lname, $email, $sex, $birth_date, $class_id, $pfname, $plname, $phone) {
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // 1. Insert parent
        $sql = "INSERT INTO parents (fname, lname, phone, email) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Parent prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssss", $pfname, $plname, $phone, $email);
        
        if (!$stmt->execute()) {
            throw new Exception("Parent insert failed: " . $stmt->error);
        }
        
        $parent_id = $conn->insert_id;
        $stmt->close();
        
        // 2. Insert student
        $sql = "INSERT INTO students (fname, lname, sex, birth_date, class_id, parent_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Student prepare failed: " . $conn->error);
        }
        
        // Make sure $class_id and $parent_id are integers
        $class_id_int = (int)$class_id;
        $parent_id_int = (int)$parent_id;
        
        $stmt->bind_param("ssssii", 
            $fname, 
            $lname, 
            $sex, 
            $birth_date, 
            $class_id_int, 
            $parent_id_int
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Student insert failed: " . $stmt->error);
        }
        
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        error_log("addStudent Error: " . $e->getMessage());
        return false;
    }
}


function getParents($conn){
    $parents = [];
    $sql = "SELECT * FROM parents";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $parents[] = $row;
    } 
    return $parents;
}

function getParent($conn,$id){
    $sql = "SELECT * FROM parents WHERE id = $id";
    $result = $conn->query($sql);
    $parent = $result->fetch_assoc();
    return $parent;
}


function editParent($conn ,$parentId, $parentFname, $parentLname, $parentPhone, $parentEmail){
    $sql = "UPDATE parents SET fname = ?, lname = ?, phone = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $parentFname, $parentLname, $parentPhone, $parentEmail, $parentId);
    return $stmt->execute();
}


function deleteClass($conn, $class_id){
    $sql = "DELETE FROM classes WHERE id = $class_id";
    $conn->query($sql);
}


function getStudentInfo($conn,$id){
    $sql = "SELECT s.*, p.fname AS parent_fname, p.lname AS parent_lname, p.phone AS parent_phone, p.email AS parent_email
    FROM students s
    LEFT JOIN parents p ON s.parent_id = p.id
    WHERE s.id = $id";
    $result = $conn->query($sql);
    $student = $result->fetch_assoc();
    return $student;
}

function deleteStudent(mysqli $conn, int $id): bool
{
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("DELETE FROM attendance WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM marks WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function editStudent($conn,$studentId, $Fname, $Lname, $bd, $sex){
    $sql = "UPDATE students SET fname = ?, lname = ?, birth_date = ?, sex = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $Fname, $Lname, $bd, $sex, $studentId);
    return $stmt->execute();
}

function deleteMessage($conn, $message_id) {
    $sql = "DELETE FROM messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    return $stmt->execute();
}


function report_bug($conn, $linked_id, $role, $descreption, $title) {
    $sql = "INSERT INTO bugs 
        (linked_id, role, description, title)
        VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "isss",
        $linked_id,
        $role,
        $descreption,
        $title
    );

    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    return $result;
}

function getBugs($conn){
    $bugs = [];
    $sql = "SELECT 
    b.*,
    CASE 
        WHEN b.role = 'admin' THEN a.fname
        WHEN b.role = 'teacher' THEN t.fname
        WHEN b.role = 'student' THEN s.fname
    END AS fname,
    CASE 
        WHEN b.role = 'admin' THEN a.lname
        WHEN b.role = 'teacher' THEN t.lname
        WHEN b.role = 'student' THEN s.lname
    END AS lname
FROM bugs b
LEFT JOIN admins a 
    ON b.role = 'admin' AND b.linked_id = a.id
LEFT JOIN teachers t 
    ON b.role = 'teacher' AND b.linked_id = t.id
LEFT JOIN students s 
    ON b.role = 'student' AND b.linked_id = s.id
ORDER BY b.date DESC;
";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bugs[] = $row;
        }
    }
    return $bugs;
}

function markBugAsSolved($conn,$id){
    $sql = "DELETE FROM `bugs` WHERE id = $id";
    return $conn->query($sql);
}

?>