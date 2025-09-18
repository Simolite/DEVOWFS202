async function getTerms(){
    let url = '../api/get_terms.php';
    try {
        let response = await fetch(url ,{headers: {'Accept': 'application/json'}} );
        let data = await response.json();
        return data;
    } catch (error){
        console.error("Error fetching terms:", error?.message || error);
    }
};

async function populateTerms() {
    terms = await getTerms();
    if (!terms) return;
    let list = document.getElementById('term');
    terms.forEach(term => {
        let option = document.createElement('option');
        option.innerText = term.name;
        option.value = term.id;
        list.appendChild(option);
    });
};

async function getSubjects(){
    let url = '../api/get_subjects.php';
    try{
        let response = await fetch(url, {headers: {'Accept': 'application/json'}});
        let data = await response.json();
        return data;
    } catch (error) {
        console.error("Error fetching subjects:", error?.message || error);
    }
};

async function populateSubjects() {
    let subjects = await getSubjects();
    if (!subjects) return;
    let list = document.getElementById('subject');
    subjects.forEach(subject => {
        let option = document.createElement('option');
        option.innerText = subject.name;
        option.value = subject.id;
        list.appendChild(option);
    });
    let option = document.createElement('option');
    option.innerText = "All subjects";
    option.value = "all";
    list.appendChild(option);
}

async function getMarks() {
    let term = document.getElementById('term').value;
    let subjectSelect = document.getElementById('subject');
    let subjectId = subjectSelect.value;
    let subjectName = subjectSelect.options[subjectSelect.selectedIndex].text;
    
    let subjects;
    if (subjectId === 'all') {
        subjects = 'all';
    } else {
        subjects = encodeURIComponent(JSON.stringify([{ id: subjectId, name: subjectName }]));
    }

    let url = `../api/get_marks.php?term=${term}&sub=${subjects}`;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        let data = await response.json();
        return data;
    } catch (error) {
        console.error("Error fetching marks:", error?.message || error);
    }
}

async function populateMarks() {
    let marks = await getMarks();
    if (!marks || Object.keys(marks).length === 0) return;

    let tbody;
    if (document.querySelector('#marks_section tbody')) {
        tbody = document.querySelector('#marks_section tbody');
        tbody.innerHTML = "";
    } else {
        let table = document.createElement('table');
        table.id = "marks_table";
        tbody = document.createElement('tbody');
        let thead = document.createElement('thead');
        
        let trHead = document.createElement('tr');
        let th1 = document.createElement('th');
        let th2 = document.createElement('th');
        let th3 = document.createElement('th');
        
        th1.innerText = 'Subject';
        th2.innerText = 'Mark';
        th3.innerText = 'Exam Date';
        
        trHead.append(th1, th2, th3);
        thead.appendChild(trHead);

        table.append(thead, tbody);
        document.querySelector('#marks_section').appendChild(table);
    }

    for (const subject in marks) {
        const subjectMarks = marks[subject];

        subjectMarks.forEach(mark => {
            let tr = document.createElement('tr');
            let td1 = document.createElement('td');
            let td2 = document.createElement('td');
            let td3 = document.createElement('td');

            td1.innerText = subject;   
            td2.innerText = mark.mark;    
            td3.innerText = mark.exam_date; 

            tr.append(td1, td2, td3);
            tbody.appendChild(tr);
        });
    }

}


function resetMarks(){
    if(document.querySelector('table')){
        document.querySelector('#marks_section table').remove();
    };
};

populateTerms();

populateSubjects();

document.querySelector("#getmarks").addEventListener('click',async()=>{
    populateMarks();
});

document.querySelector("#delmarks").addEventListener('click',()=>{
    resetMarks();
});



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
    let url = `../api/getAnnouncements.php?audience=all&class_id=${userInfo.class_id}`;
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
document.querySelector("#report").addEventListener('click',()=>{
    toggleSection('report','report_section');
});



async function getAttInfo(){
    let url = `../api/getAttendanceInfo.php`;
    let data;
    let num = document.querySelector("#absnum");
    let absdays = document.querySelector("#absdays");
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching marks:", error?.message || error);
    }
    data.forEach(absent=>{
        let tr = document.createElement("tr");
        let td1 = document.createElement("td");
        let td2 = document.createElement("td");
        td1.innerText = absent.subject_name;
        td2.innerText = absent.date;
        tr.append(td1,td2);
        document.querySelector("#attendance_section tbody").appendChild(tr);
    });
    num.innerText = data.length;
    absdays.innerText = countUniqueDates(data);
}

function countUniqueDates(records) {
    let dates = records.map(r => r.date); 
    let uniqueDates = new Set(dates);  
    return uniqueDates.size;          
}
getAttInfo();


async function getReport(){
    let url = `../api/getReport.php`;
    let data;
    let tbody = document.querySelector("#report_section tbody")
        try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching marks:", error?.message || error);
    }
        data.forEach(report=>{
            let tr = document.createElement("tr");
            let td1 = document.createElement("td");
            let td2 = document.createElement("td");
            let td3 = document.createElement("td");
            let td4 = document.createElement("td");
            let td5 = document.createElement("td");
            let td6 = document.createElement("td");
            let td7 = document.createElement("td");
            let btn = document.createElement("a");
            td1.innerText = report.term_name;
            td2.innerText = report.start_date;
            td3.innerText = report.end_date;
            td4.innerText = report.average_score;
            td5.innerText = report.rank;
            td6.innerText = report.comments;
            btn.innerText = "Click here to get the report"
            btn.href = report.url;
            td7.appendChild(btn);
            tr.append(td1,td2,td3,td4,td5,td6,td7);
            tbody.appendChild(tr);
        });
};


getReport();


async function getTeachers(){
    let url = `../api/getStudentTeachers.php`;
    let data;
        try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching marks:", error?.message || error);
    }
    let select = document.getElementById('recipient');
    data.forEach(teacher =>{
        let option = document.createElement('option');
        option.innerText = " الاستاذ(ة) " + teacher.fname + " " + teacher.lname;
        option.value = teacher.id;
        select.appendChild(option);
    });
};

 getTeachers();


 document.getElementById("logout_btn").addEventListener('click',()=>{
    window.location.href = "../login/logout.php";
 });

 async function sendMessages(){
    let recipient = document.getElementById("recipient").value;
    const type = document.getElementById("messageType").value;
    const subject = document.getElementById("message_subject").value;
    const content = document.getElementById("messageContent").value;
    const statusDiv = document.getElementById("messageStatus");
    let role;
    if(recipient == 'admin'){
        role = 'admin';
        recipient = 1;
    }else {
        role = 'teacher';
    }

    
    if (!recipient || !type || !subject || !content) {
        statusDiv.textContent = "⚠️ يرجى ملء جميع الحقول";
        statusDiv.className = "mt-4 p-3 bg-red-100 text-red-800 rounded transition-all duration-300";
        statusDiv.classList.remove("hidden");
        return;
    }


    statusDiv.textContent = "⏳ جاري إرسال الرسالة...";
    statusDiv.className = "mt-4 p-3 bg-blue-100 text-blue-800 rounded transition-all duration-300";
    statusDiv.classList.remove("hidden");

    const url = "../api/sendMessages.php";

    const formData = new FormData();
    formData.append("reciver_id", recipient);
    formData.append("reciver_role", role);
    formData.append("message", content);
    formData.append("title", subject);
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

    if (data == 200) {
        statusDiv.textContent = " تم إرسال الرسالة...";
        document.getElementById("messageForm").reset();
    } else {
        statusDiv.textContent = "حدث خطأ ,المرجو المحاولة لاحقا "
    }

    console.log();
    




 };


document.getElementById("messageForm").addEventListener("submit", function(e) {
    e.preventDefault();
    sendMessages();

    setTimeout(() => {
        document.getElementById("messageStatus").classList.add("hidden");
    }, 10000);
});

document.getElementById("time_table_img").addEventListener('click',()=>{
    window.location.href = document.getElementById("time_table_img").src;
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