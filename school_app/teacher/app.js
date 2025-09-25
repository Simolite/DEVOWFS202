async function getClasses(dest) {

    let url = `../api/getTeacherClasses.php`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching classes:", error?.message || error);
    }
    data.forEach(Tclass => {
        let option = document.createElement('option');
        option.value = Tclass.id;
        option.innerText = Tclass.name;
        document.querySelector("#"+dest).appendChild(option);
    });

}

async function getSubjets(dest) {

    let url = `../api/getTeacherSubjets.php`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching subjects:", error?.message || error);
    }
    data.forEach(sub => {
        let option = document.createElement('option');
        option.value = sub.id;
        option.innerText = sub.name;
        document.querySelector("#"+dest).appendChild(option);
    });

    
}

async function getStudents(dest) {

    let class_id = document.querySelector('#'+dest).value;

    let url = `../api/getClassStudents.php?class_id=${class_id}`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching students:", error?.message || error);
    }
    if(dest == 'Attclass'){
        return data;
    }
    data.forEach(student => {
        let option = document.createElement('option');
        option.value = student.id;
        option.innerText = `${student.fname} ${student.lname}`;
        document.querySelector("#student").appendChild(option);
    });    
}

async function getTerms(){
    let url = '../api/get_terms.php';
    let data;
    try {
        let response = await fetch(url ,{headers: {'Accept': 'application/json'}} );
        data = await response.json();
    } catch (error){
        console.error("Error fetching terms:", error?.message || error);
    }
    data.forEach(term => {
        let option = document.createElement('option');
        option.value = term.id;
        option.innerText = term.name;
        document.querySelector("#term").appendChild(option);
    });
};
 
getClasses('Markclass');
getClasses('Attclass');


// Marks select change
document.querySelector("#Markclass").addEventListener('change', () => {
    let lists = document.querySelector("#Markinput");
    if (lists) {
        Array.from(lists.children).forEach(list => {
            if (list.tagName === 'SELECT' && list.id !== 'Markclass') {
                Array.from(list.options).forEach(option => {
                    if (option.value != "0") option.remove();
                });
            }
        });
    }

    getSubjets("Marksubject");
    getStudents("Markclass");
    getTerms();
});

// Attendance select change
document.querySelector("#Attclass").addEventListener('change', () => {
    let lists = document.querySelector("#Attinput");
    if (lists) {
        Array.from(lists.children).forEach(list => {
            if (list.tagName === 'SELECT' && list.id !== 'Attclass') {
                Array.from(list.options).forEach(option => {
                    if (option.value != "0") option.remove();
                });
            }
        });
    }

    getSubjets("Attsub");
});


async function attendanceList(){
    let students = await getStudents("Attclass");
    if(document.querySelector("#attendance_section tbody")){
        document.querySelector("#attendance_section tbody").remove();
    }
    let tbody = document.createElement("tbody");
    students.forEach(student=>{
        let tr = document.createElement("tr");
        let td1 = document.createElement("td");
        let td2 = document.createElement("td");
        let chkbx = document.createElement("input");
        chkbx.type = "checkbox";
        td1.innerText = student.fname + " " +student.lname;
        chkbx.value = student.id;
        chkbx.classList.add('studentBox');
        td2.appendChild(chkbx);
        tr.append(td1,td2);
        tbody.appendChild(tr);

    })
    document.querySelector("#attendance_section table").appendChild(tbody);
}

document.getElementById('getAttList').addEventListener('click',()=>{
    let Attclass = document.getElementById("Attclass").value;
    let Attsub = document.getElementById("Attsub").value;
    if(Attclass == 0||Attsub ==0){
        alert("Please fill the class and subject");
        return;
    }
    attendanceList();
});

function addMark() {
    let student_id = document.querySelector("#student").value;
    let subject_id = document.querySelector("#Marksubject").value;
    let mark = document.querySelector("#mark").value;
    let term = document.querySelector("#term").value;
    let date = document.querySelector("#Markdate").value;

    let body = `student_id=${encodeURIComponent(student_id)}&` +
               `subject_id=${encodeURIComponent(subject_id)}&` +
               `mark=${encodeURIComponent(mark)}&` +
               `term=${encodeURIComponent(term)}&` +
               `date=${encodeURIComponent(date)}`;

    fetch('../api/addMark.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert("Error: " + data.error);
        } else {
            alert("Mark added successfully!");
        }
    })
    .catch(err => console.error("Fetch error:", err));
}


document.querySelector('#Marksubmit').addEventListener('click', () => {
    let Markclass = document.getElementById("Markclass").value;
    let Marksubject = document.getElementById("Marksubject").value;
    let student = document.getElementById("student").value;
    let term = document.getElementById("term").value;
    let Markdate = document.getElementById("Markdate").value;
    let mark = document.getElementById("mark").value;
    if (!Markclass ||!Marksubject ||!student ||!term ||!Markdate ||!mark){
        alert("Please fill all the fields");
        return;
    }
    addMark();
});


function toggleSection(activeBtn,showId){
    document.querySelector('.selected').classList.remove("selected");
    document.getElementById(activeBtn).classList.add("selected");
    let mains = document.querySelectorAll("main");
    let mainsArray = Array.from(mains);
    mainsArray.forEach(elem =>{
        elem.classList.add('hidden');
    });
    document.getElementById(showId).classList.remove('hidden');
}

document.querySelector("#marks").addEventListener('click',()=>{
    toggleSection('marks','marks_section');
});

document.querySelector("#notifaction").addEventListener('click',()=>{
    toggleSection('notifaction','notifactions_section');
});

document.querySelector("#messages").addEventListener('click',()=>{
    toggleSection('messages','messages_section');
});


async function getMessages(){
    let url = '../api/getMessages.php';
    let data;
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching messages:", error?.message || error);
    }
    let tbody = document.getElementById("messagesList");
    data.forEach(message =>{
        let tr = document.createElement('tr');
        let td1 = document.createElement('td');
        let td2 = document.createElement('td');
        let td3 = document.createElement('td');
        let td4 = document.createElement('td');
        let td5 = document.createElement('td');
        td1.innerText = message.sender_name + " ( " +message.sender_role + " )";
        td2.innerText = message.title;
        td3.innerText = message.message;
        td4.innerText = message.type;
        td5.innerText = message.sent_at;
        tr.append(td1,td2,td3,td4,td5);
        tbody.appendChild(tr);
    });
    
}

 getMessages();



let userInfo;

async function getUserInfo() {
    let url = `../api/getUserInfo.php`;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        userInfo = await response.json();
        return userInfo;     
    } catch (error) {
        console.error("Error fetching Info:", error?.message || error);
    }
}

async function getAnnouncements(){
    await getUserInfo();
    let url = `../api/getAnnouncements.php?audience=all`;
    let data;
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching Announcements:", error?.message || error);
    };
    let tbody = document.querySelector("#notifactions_section tbody");
    data.forEach(announ =>{
        let tr = document.createElement('tr');
        let td1 = document.createElement('td');
        let td2 = document.createElement('td');
        let td3 = document.createElement('td');
        td1.innerText = announ.title;
        td2.innerText = announ.body;
        td3.innerText = announ.created_at;
        tr.append(td1,td2,td3);
        tbody.appendChild(tr);
    }) 
}

getAnnouncements();

function toggleSection(activeBtn,showId){
    document.querySelector('.selected').classList.remove("selected");
    document.getElementById(activeBtn).classList.add("selected");
    let mains = document.querySelectorAll("main");
    let mainsArray = Array.from(mains);
    mainsArray.forEach(elem =>{
        elem.classList.add('hidden');
    });
    document.getElementById(showId).classList.remove('hidden');
}

document.querySelector("#marks").addEventListener('click',()=>{
    toggleSection('marks','marks_section');
});

document.querySelector("#notifaction").addEventListener('click',()=>{
    toggleSection('notifaction','notifactions_section');
});
document.querySelector("#attendance").addEventListener('click',()=>{
    toggleSection('attendance','attendance_section');
});



function addAtt(){
    let Students = document.querySelectorAll('.studentBox');
    let absentIds = Array.from(Students).filter(cb => cb.checked).map(cb => cb.value);
    let presentIds = Array.from(Students).filter(cb => !cb.checked).map(cb => cb.value);
    let subjectId = document.querySelector("#Attsub").value;
    let date = document.querySelector("#Attdate").value;

    absentIds.forEach(absent=>{
        let body = `student_id=${encodeURIComponent(absent)}&` +
                `subject_id=${encodeURIComponent(subjectId)}&` +
                `date=${encodeURIComponent(date)}&` +
                `stat=absent`;

        fetch('../api/addAtt.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error: " + data.error);
            }
        })
        .catch(err => console.error("Fetch error:", err));   
    });

    presentIds.forEach(present=>{
        let body = `student_id=${encodeURIComponent(present)}&` +
                `subject_id=${encodeURIComponent(subjectId)}&` +
                `date=${encodeURIComponent(date)}&` +
                `stat=present`;

        fetch('../api/addAtt.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(response => response.json())
        .then(data => { 
            if (data.error) {
                console.error("Error: " + data.error);
            }
        })
        .catch(err => console.error("Fetch error:", err));   
    });
}


document.querySelector("#submitAtt").addEventListener('click',()=>{
    let date = document.getElementById('Attdate').value;
    if(!date){
        alert('Please select a date ');
        return;
    }
    addAtt();
    alert("attandance added seccsufully")
});


document.getElementById("time_table_img").addEventListener('click',()=>{
    window.location.href = document.getElementById("time_table_img").src;
});


async function sendMessagesRoleRef(){
    let recipient_role = document.getElementById("recipient_role");
    let recipient_class = document.getElementById("recipient_class");
    let recipient = document.getElementById("recipient");
    let Srecipient = document.getElementById("Srecipient");
    if(recipient_class.parentElement.classList.contains('hidden')){
        recipient_class.parentElement.classList.remove("hidden");
    };
    if(recipient.parentElement.classList.contains('hidden')){
        recipient.parentElement.classList.remove("hidden");
    };
    if(Srecipient.parentElement.classList.contains('hidden')){
        Srecipient.parentElement.classList.remove("hidden");
    };
    if(recipient_role.value == 'admin'){
        recipient_class.parentElement.classList.add("hidden");
        recipient.parentElement.classList.add("hidden");
        Srecipient.parentElement.classList.add("hidden");
    }else if (recipient_role.value == 'teacher'){
        recipient_class.parentElement.classList.add("hidden");
        Srecipient.parentElement.classList.add("hidden");
    }else if (recipient_role.value == 'student'){
        recipient.parentElement.classList.add("hidden");
        let classes = await getTeacherClasses();
        classes.forEach(Tclass =>{
            let option = document.createElement('option');
            option.innerText = Tclass.name;
            option.value = Tclass.id;
            recipient_class.appendChild(option);
        })
    }
}

document.getElementById("recipient_role").addEventListener("change",()=>{
    sendMessagesRoleRef();
});

async function sendMessagesClassRef() {
    let Srecipient = document.getElementById('Srecipient');

    Array.from(Srecipient.children).forEach(stud => {
        if (stud.value != 0) {
            stud.remove();
        }
    });

     let students = await getClassStudents(document.getElementById("recipient_class").value);
    

    students.forEach(stud => {
        let option = document.createElement("option");
        option.text = stud.fname + ' ' + stud.lname;
        option.value = stud.id;
        Srecipient.appendChild(option);
    });
}

document.getElementById("recipient_class").addEventListener("change",()=>{
    sendMessagesClassRef();
});

async function getClassStudents(class_id){
    let url = `../api/getClassStudents.php?class_id=${class_id}`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching students:", error?.message || error);
    }
    return data;
};

async function getTeacherClasses() {

    let url = `../api/getTeacherClasses.php`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching classes:", error?.message || error);
    }
    return data;

}

async function sendMessage(){
    let reciver_role = document.getElementById("recipient_role").value;
    let reciver_id;
    let message;
    let title;
    let type;
    if(reciver_role == 'admin'){
        reciver_id = 1;
    }else if (reciver_role == 'teacher'){
        reciver_id = document.getElementById("recipient").value;
    }else if (reciver_role == 'student'){
        reciver_id = document.getElementById("Srecipient").value;
    }
    message = document.getElementById("messageContent").value;
    title = document.getElementById("message_subject").value;
    type = document.getElementById("messageType").value;
    console.log(reciver_role,reciver_id,message,title,type);


    const url = "../api/sendMessages.php";

    const formData = new FormData();
    formData.append("reciver_id", reciver_id);
    formData.append("reciver_role", reciver_role);
    formData.append("message", message);
    formData.append("title", title);
    formData.append("type", type);

    let data;
    try {
    let response = await fetch(url, {
        method: "POST",
        body: formData
    });

    data = await response.json();
    } catch (error) {
    console.error("Error sending message:", error?.message || error);
    }
}

document.getElementById("submitMessage").addEventListener("click", (e)=>{
    e.preventDefault();
    sendMessage();
})