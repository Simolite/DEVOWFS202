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

async function getAnnouncements() {
    await getUserInfo();
    const url = `../api/getAnnouncements.php?audience=all&class_id=all`;
    let data = [];

    try {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
    } catch (error) {
        console.error("Error fetching Announcements:", error?.message || error);
    }

    const tbody = document.querySelector("#notifaction_section tbody");
    tbody.innerHTML = '';

    data.forEach(announ => {
        const tr = document.createElement('tr');

        const td1 = document.createElement('td');
        td1.innerText = announ.title;

        const td2 = document.createElement('td');
        td2.innerText = announ.body;

        const td3 = document.createElement('td');
        td3.innerText = announ.created_at;

        const td4 = document.createElement('td');
        const btnDelete = document.createElement('button');
        btnDelete.innerText = "حذف";
        btnDelete.className = "bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600";

        btnDelete.addEventListener('click', async () => {
            if (confirm("هل أنت متأكد من حذف هذا الإعلان؟")) {
                try {
                    const deleteUrl = `../api/deleteAnnouncement.php?id=${announ.id}`;
                    const res = await fetch(deleteUrl, { method: 'GET' }); 
                    const result = await res.json();
                    if (result ==200) {
                        tr.remove(); 
                    } else {
                        alert(result.error || "حدث خطأ أثناء الحذف.");
                    }
                } catch (err) {
                    console.error("Error deleting announcement:", err);
                }
            }
        });

        td4.appendChild(btnDelete);
        tr.append(td1, td2, td3, td4);
        tbody.appendChild(tr);
    });
}


async function getAccounts(){
    let role = document.querySelector("#accRole").value;    
    let accounts;
    let url = `../api/getAccount.php?role=${encodeURIComponent(role)}`;
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });       
        accounts = await response.json();
    } catch (error) {
        console.error("Error fetching Account:", error?.message || error);
    }
    let list = document.querySelector("select#accountSelect");
    accounts.forEach(account=>{
        let option = document.createElement("option");
        option.innerText = account.username;
        option.value = account.id;
        list.append(option);
    })
}

function toggleSection(id){
    document.querySelector('.selected').classList.remove("selected");
    document.getElementById(id).classList.add("selected");
    let mains = document.querySelectorAll("main");
    let mainsArray = Array.from(mains);
    mainsArray.forEach(elem =>{
        elem.classList.add('hidden');
    });
    document.getElementById(id+"_section").classList.remove('hidden');
}

document.querySelector("#notifaction").addEventListener('click',()=>{
    toggleSection('notifaction');
});

document.querySelector("#account").addEventListener('click',()=>{
    toggleSection('account');
});

document.querySelector("#attendance").addEventListener('click',()=>{
    toggleSection('attendance');
});

document.querySelector("#class").addEventListener('click',()=>{
    toggleSection('class');
});

document.querySelector("#marks").addEventListener('click',()=>{
    toggleSection('marks');
});

document.querySelector("#student").addEventListener('click',()=>{
    toggleSection('student');
});

// document.querySelector("#teacher").addEventListener('click',()=>{
//     toggleSection('teacher');
// });

// document.querySelector("#term").addEventListener('click',()=>{
//     toggleSection('term');
// });

document.querySelector("#messages").addEventListener('click',()=>{
    toggleSection('messages');
});

document.querySelector("#parents").addEventListener('click',()=>{
    toggleSection('parents');
});


document.querySelector("#accRole").addEventListener("change",()=>{
    let list = document.querySelector("select#accountSelect");
    list.innerHTML='';
    getAccounts();
});


getAnnouncements();

const targetSelect = document.getElementById('target');
const container = document.getElementById('dynamicContainer');

function clearDynamicSelects() {
    container.innerHTML = '';
}

function createSelect(id, placeholder) {
    const select = document.createElement('select');
    select.id = id;
    select.className = "dynamic-select w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium " +
                       "focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 " +
                       "hover:border-blue-400 transition-colors mt-2";

    const defaultOption = document.createElement('option');
    defaultOption.value = "0";
    defaultOption.selected = true;
    defaultOption.disabled = true;
    defaultOption.innerText = placeholder;

    select.appendChild(defaultOption);
    return select;
}

async function fetchOptions(apiUrl,select,optText = 'name') {
    try {
        const res = await fetch(apiUrl, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            if(optText == 'name'){
                opt.innerText = item[optText];
            }else{
                opt.innerText = `${item.fname} ${item.lname}`;
            }
            
            select.appendChild(opt);
        });
    } catch (err) {
        console.error("Error fetching options:", err);
    }
}


targetSelect.addEventListener('change', async () => {
    clearDynamicSelects();
    const value = targetSelect.value;



    if (value === "classes") {
        const classSelect = createSelect('classSelect', 'اختر القسم');
        container.appendChild(classSelect);
        await fetchOptions('../api/getClasses.php', classSelect);
    }


});


async function addAnnouncement(title, body, targ_audience, id) {
    let url = "../api/addAnnouncement.php"; 

    try {
        let response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                title: title,
                body: body,
                audience: targ_audience,
                id: id
            })
        });
        
        let data = await response.json();

        if (data.success) {
            alert("تمت إضافة الإعلان بنجاح ✅");
        } else {
            alert("فشل في الإضافة ❌: " + (data.error || "خطأ غير معروف"));
        }
    } catch (error) {
        console.error("خطأ أثناء إرسال الإعلان:", error);
    }
}


document.getElementById('add_ann').addEventListener('click',(e)=>{
    e.preventDefault();
    let title = document.getElementById('notifTitle').value;
    let body = document.getElementById('notifBody').value;
    let audience = document.getElementById('target').value;
    let id;
    if(audience=='classes'){
        id = document.getElementById('classSelect').value;
    }
    addAnnouncement(title, body, audience, id);
    getAnnouncements();
})


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

async function changePassword(id,pass) {
    let url = "../api/changePassword.php";  

    try {
        let response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                id: id,
                pass: pass,
            })
        });
        
        
        let data = await response.json();
        

        if (data.success) {
            alert("تمت  تغير كلمة السر بنجاح ✅");
        } else {
            alert("فشل   ❌: " + (data.error || "خطأ غير معروف"));
        }
    } catch (error) {
        console.error("خطأ   :", error);
    }
}

document.getElementById("applyBtn").addEventListener("click",()=>{
    let id = document.getElementById("accountSelect").value;
    let pass = document.getElementById("password").value;
    if(!id){
        alert('please select the account');
        return;
    }else if (!pass){
        alert('please enter the password');
        return;
    }
    
    changePassword(id,pass);
})

getMessages();

async function getClasses(dest) {

    let url = `../api/getClasses.php`;
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

getClasses("Attclass");

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
    if(dest == 'Attclass'||dest == 'AttclassDell'||dest == "Markclass" || dest == "studentInfoSelectClass" || dest == "ClassSelectStudentDelete"){
        return data;
    }else{
        data.forEach(student => {
            let option = document.createElement('option');
            option.value = student.id;
            option.innerText = `${student.fname} ${student.lname}`;
            document.querySelector("#student").appendChild(option);
        });
    }
}

document.querySelector("#Attclass").addEventListener('change', () => {
    let lists = document.querySelector("#Attinput");
    if (lists) {
        Array.from(lists.children).forEach(list => {
            if (list.tagName === 'SELECT' && list.id !== 'Attclass') {
                Array.from(list.options).forEach(option => {
                    if (option.value != "0") {option.remove();}
                });
            }
        });
    }

    getSubjets("Attsub");
});

async function getSubjets(dest) {

    let url = `../api/getTeacherSubjets.php?teacher_id=all`;
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

document.getElementById('getAttList').addEventListener('click',()=>{
    let Attclass = document.getElementById("Attclass").value;
    let Attsub = document.getElementById("Attsub").value;
    if(Attclass == 0||Attsub ==0){
        alert("Please fill the class and subject");
        return;
    }
    attendanceList();
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
    alert("attandance added seccsufully");
    let list =  document.querySelectorAll("#studentsAttList tr");
    list.forEach(elem =>{
        console.log(elem);
        
        elem.remove();
    })
});

getClasses("AttclassDell");

document.getElementById("AttclassDell").addEventListener('change',async()=>{
    let students = await getStudents("AttclassDell");
    let select = document.getElementById("AttsubDell");
    students.forEach(stud =>{
        let option = document.createElement("option");
        option.innerText = stud.fname + " " + stud.lname;
        option.value = stud.id;
        select.appendChild(option);
    })

});

async function getAttandance(stud_id){
    let url = `../api/getAttendanceInfo.php?id=${encodeURIComponent(stud_id)}`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        return data = await response.json();
    } catch (error) {
        console.error("Error fetching attendance:", error?.message || error);
    }
}

document.getElementById("getAttListDell").addEventListener("click",async()=>{
    let tbody = document.getElementById("attDellTbody");
    tbody.innerHTML = "";
    let stud_id = document.getElementById("AttsubDell").value;
    let attandance = await getAttandance(stud_id);
    
    attandance.forEach(att=>{
        let tr = document.createElement("tr");
        let td1 = document.createElement("td");
        let td2 = document.createElement("td");
        let td3 = document.createElement("td");
        let box = document.createElement("input");
        box.type = 'checkbox';
        td1.innerText = att.subject_name;
        td2.innerText = att.date;
        box.value = att.attendance_id;
        td3.appendChild(box);
        tr.append(td1,td2,td3);
        document.getElementById("attDellTbody").appendChild(tr);
    })
})

document.getElementById("submitAttDell").addEventListener("click",()=>{
    dellAttendance();
})

async function dellAttendance() {
    let attendance = document.querySelectorAll("#attDellTbody input[type='checkbox']:checked");
    attendance.forEach(att => {
        dellAttendanceApi(att.value);
    });    
}

async function dellAttendanceApi(att_id) {
    let url = `../api/dellAttendance.php?id=${att_id}`;
        
    try {
        await fetch(url, { headers: { 'Accept': 'application/json' } });
        let tbody = document.getElementById("attDellTbody");
        tbody.innerHTML = "";
        let stud_id = document.getElementById("AttsubDell").value;
        let attandance = await getAttandance(stud_id);
        
        attandance.forEach(att=>{
            let tr = document.createElement("tr");
            let td1 = document.createElement("td");
            let td2 = document.createElement("td");
            let td3 = document.createElement("td");
            let box = document.createElement("input");
            box.type = 'checkbox';
            td1.innerText = att.subject_name;
            td2.innerText = att.date;
            box.value = att.attendance_id;
            td3.appendChild(box);
            tr.append(td1,td2,td3);
            document.getElementById("attDellTbody").appendChild(tr);
        alert(`Attendance deleted`);
        })
    } catch (error) {
        console.error("Error deleting attendance:", error?.message || error);
    }
}
 
const recipientSelect = document.getElementById("recipient");
const messageForm = document.getElementById("messageForm");
const dynamicRecipientContainer = document.createElement("div"); 
dynamicRecipientContainer.id = "dynamicRecipientContainer";
messageForm.insertBefore(dynamicRecipientContainer, messageForm.children[1]);

function clearDynamicRecipient() {
    dynamicRecipientContainer.innerHTML = "";
}

function createSelect(id, placeholder) {
    const select = document.createElement("select");
    select.id = id;
    select.className =
        "w-full p-3 border border-gray-300 rounded-lg mt-2";
    const opt = document.createElement("option");
    opt.value = "0";
    opt.disabled = true;
    opt.selected = true;
    opt.innerText = placeholder;
    select.appendChild(opt);
    return select;
}





recipientSelect.addEventListener("change", async () => {
    clearDynamicRecipient();
    const value = recipientSelect.value;

    if (value === "student") {

        const classSelect = createSelect("msgClassSelect", "اختر القسم");
        dynamicRecipientContainer.appendChild(classSelect);
        await fetchOptions("../api/getClasses.php", classSelect);

        const studentSelect = createSelect("msgStudentSelect", "اختر الطالب");
        dynamicRecipientContainer.appendChild(studentSelect);

        // When class changes → load students
        classSelect.addEventListener("change", async () => {
            studentSelect.innerHTML = "";
            const defaultOpt = document.createElement("option");
            defaultOpt.value = "0";
            defaultOpt.disabled = true;
            defaultOpt.selected = true;
            defaultOpt.innerText = "اختر الطالب";
            studentSelect.appendChild(defaultOpt);

            await fetchOptions(`../api/getClassStudents.php?class_id=${classSelect.value}`,studentSelect, 'full_name');

        });
    }

    if (value === "teacher") {
        const teacherSelect = createSelect("msgTeacherSelect", "اختر الأستاذ");
        dynamicRecipientContainer.appendChild(teacherSelect);
        await fetchOptions("../api/getTeachers.php", teacherSelect,'full_name');
        teacherSelect.addEventListener("change",()=>{
            let id = teacherSelect.value
        })
    }
});

document.getElementById("message_send_btn").addEventListener('click',(e)=>{
    e.preventDefault();
    sendMessage();
})

async function sendMessage() {
    let receiver_role = document.getElementById("recipient").value;
    let receiver_id;

    if (receiver_role == 'admin') {
        receiver_id = 1;
    } else if (receiver_role == 'teacher') {
        receiver_id = document.getElementById("msgTeacherSelect").value;
    } else if (receiver_role == 'student') {
        receiver_id = document.getElementById("msgStudentSelect").value;
    }

    let message = document.getElementById("messageContent").value;
    let title = document.getElementById("message_subject").value;
    let type = document.getElementById("messageType").value;

    if (!message.trim() || !title.trim()) {
        alert('Please fill in all required fields');
        return;
    }

    let formData = new FormData();
    formData.append("receiver_id", receiver_id);
    formData.append("receiver_role", receiver_role);
    formData.append("message", message);
    formData.append("title", title);
    formData.append("type", type);

    try {
        let resp = await fetch("../api/sendMessages.php", {
            method: "POST",
            body: formData,
        });
        
        let result = await resp.json();
        
        
        if (result.success) {
            alert('Message sent successfully!');
            document.getElementById("messageContent").value = '';
            document.getElementById("message_subject").value = '';
        } else {
            alert('Error: ' + result.error);
        }
        
    } catch (error) {
        console.error("Error sending message:", error);
        alert('Network error occurred. Please try again.');
    }
}

document.querySelector("#Markclass").addEventListener('change', async () => {
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
    let students = await getStudents("Markclass");
    students.forEach(stud => {
        let option = document.createElement('option');
        option.value = stud.id;
        option.innerText = `${stud.fname} ${stud.lname}`;
        document.querySelector("#student_mark_select").appendChild(option);
    });
    getTerms();
});

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
        document.querySelector("#term_mark_select").appendChild(option);
    });
};

getClasses('Markclass');


function addMark() {
    let student_id = document.querySelector("#student_mark_select").value;
    let subject_id = document.querySelector("#Marksubject").value;
    let mark = document.querySelector("#markToSubmit").value;
    let term = document.querySelector("#term_mark_select").value;
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


document.getElementById("SubmitMark").addEventListener("click",()=>{
    addMark();
})

document.getElementById("Markshow").addEventListener("click",async ()=>{showmarks()});
async function showmarks(){
    document.getElementById("marksList").innerHTML = '';
    let url = `../api/get_marks.php?student_id=${document.querySelector("#student_mark_select").value}&term=${document.querySelector("#term_mark_select").value}&sub=all`;
    let data;
    try {
        let response = await fetch(url ,{headers: {'Accept': 'application/json'}} );
        data = await response.json();
    } catch (error){
        console.error("Error fetching marks:", error?.message || error);
    }

    Object.entries(data).forEach(([subject, marks]) => {
        marks.forEach(mark => {
            let tr = document.createElement('tr');

            let td1 = document.createElement("td");
            let td2 = document.createElement("td");
            let td3 = document.createElement("td");
            let td5 = document.createElement("td");
            let btn = document.createElement("button");

            td1.innerText = subject;
            td2.innerText = mark.mark;
            td3.innerText = mark.exam_date;
            btn.innerText = "Delete";
            btn.className = "bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700";
            btn.addEventListener('click',()=>{
                dellMark(mark.id);
            })

            td5.appendChild(btn);
            tr.append(td1, td2, td3, td5);
            document.querySelector("#marksList").appendChild(tr);
        });
    });


}

async function dellMark(mark_id){
    let url = `../api/dellMark.php?mark_id=${mark_id}`;
    let data;
    try{
        let response = await fetch(url,{headers: {'Accept': 'application/json'}});
        data = await response.json();
    }catch (error){
        console.error("Error delleting marks:", error?.message || error);
    }

    showmarks();  
}


const showCreate = document.getElementById('showCreate');
const showEdit = document.getElementById('showEdit');
const showDelete = document.getElementById('showDelete');
const createSection = document.getElementById('createSection');
const editSection = document.getElementById('editSection');
const deleteSection = document.getElementById('deleteSection');

function activateTab(tab) {
    if (tab === 'create') {
        deleteSection.classList.add('hidden');
        createSection.classList.remove('hidden');
        editSection.classList.add('hidden');

        showCreate.classList.add('bg-gray-50', 'text-gray-800');
        showDelete.classList.remove('bg-gray-100', 'text-gray-600');

        showDelete.classList.add('bg-gray-100', 'text-gray-600');
        showDelete.classList.remove('bg-gray-50', 'text-gray-800');

        showEdit.classList.add('bg-gray-100', 'text-gray-600');
        showEdit.classList.remove('bg-gray-50', 'text-gray-800');
    } else if (tab === 'edit') {
        deleteSection.classList.add('hidden');
        createSection.classList.add('hidden');
        editSection.classList.remove('hidden');

        showEdit.classList.add('bg-gray-50', 'text-gray-800');
        showEdit.classList.remove('bg-gray-100', 'text-gray-600');

        showDelete.classList.add('bg-gray-100', 'text-gray-600');
        showDelete.classList.remove('bg-gray-50', 'text-gray-800');

        showCreate.classList.add('bg-gray-100', 'text-gray-600');
        showCreate.classList.remove('bg-gray-50', 'text-gray-800');
    } else if (tab === 'delete') {
        deleteSection.classList.remove('hidden');
        createSection.classList.add('hidden');
        editSection.classList.add('hidden');

        showDelete.classList.add('bg-gray-50', 'text-gray-800');
        showDelete.classList.remove('bg-gray-100', 'text-gray-600');

        showCreate.classList.add('bg-gray-100', 'text-gray-600');
        showCreate.classList.remove('bg-gray-50', 'text-gray-800');

        showEdit.classList.add('bg-gray-100', 'text-gray-600');
        showEdit.classList.remove('bg-gray-50', 'text-gray-800');
    }
}


showCreate.addEventListener('click', () => activateTab('create'));
showEdit.addEventListener('click', () => activateTab('edit'));
showDelete.addEventListener('click', () => activateTab('delete'));


document.getElementById("add_class").addEventListener('click', () => {
    if(!confirm("are you sure you want to add a class ?")){
        return;
    }
    
    let className = document.querySelector('#add_class_input').value;
    if(!className){
        alert("Please enter class name");
        return;
    }
    createClass(className);
    document.querySelector('#add_class_input').value = '';
})


async function getAllClasses(){
    let url = `../api/getClasses.php`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();        
        return data;
    } catch (error) {
        console.error("Error fetching classes:", error?.message || error);
    }
}
async function trackClassSelect() {
    let classes = await populateClassSelect("editClassSelect");

    

    document.getElementById("editClassSelect").addEventListener("change", () => {
        classes.forEach(cls =>{
            if(cls.id == document.getElementById("editClassSelect").value){
                document.getElementById("classNameEdit").value = cls.name;
                document.getElementById("classTimeEdit").value = cls.timetable_url;                
            }
        })
    });

}




trackClassSelect();


async function getTeachers(){
    let url = `../api/getTeachers.php`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
     
        data = await response.json();

        return data; 
    } catch (error) {
        console.error("Error fetching Teachers:", error?.message || error);
    }
};






async function createClass(name){
    let url = `../api/createClass.php?name=${encodeURIComponent(name)}`;
    try{
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        let data = await response.json();
        if(data.error){
            alert("Error creating class: " + data.error);
            return;
        }else{
            alert("Class created successfully!");
        }
    }catch (error) {
        console.error("Error creating class:", error?.message || error);
        alert("Error creating class:", error?.message || error);
    }
    
}

async function editClass(id, name, timetable_url){
    let url = `../api/editClass.php?id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&timetable_url=${encodeURIComponent(timetable_url)}`;
    try{
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        let data = await response.json();
        if(data.error){
            alert("Error editing class: " + data.error);
            return;
        }else{
            alert("Class edited successfully!");
        }
    }catch (error) {
        console.error("Error editing class:", error?.message || error);
        alert("Error editing class:", error?.message || error);
    }
}


document.getElementById("classSubmit").addEventListener('click',()=>{
    let id = document.getElementById("editClassSelect").value;
    let name = document.getElementById("classNameEdit").value;
    let timetable_url = document.getElementById("classTimeEdit").value;
    if(!id || !name || !timetable_url){
        alert("Please fill all required fields");
        return;
    }
    editClass(id, name, timetable_url);
});

async function addStudent(fname, lname, birthdate, class_id,sex,pfname,plname,phone,email){
    
    let url = "../api/addStudent.php";
    try {
        let response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                fname: fname,
                lname: lname,
                birthdate: birthdate,
                class_id: class_id,
                sex: sex,
                pfname: pfname,
                plname: plname,
                phone: phone,
                email: email
            })
        });
        
        
        let data = await response.json();
        

        

        if (data.success) {
            alert("تمت إضافة الطالب بنجاح ✅");
        } else {
            alert("فشل في الإضافة ❌: " + (data.error || "خطأ غير معروف"));
        }
    } catch (error) {
        console.error("خطأ أثناء إضافة الطالب:", error);
    }
}



document.getElementById("add_student").addEventListener('click',()=>{
    let fname = document.getElementById("studentFname").value;
    let lname = document.getElementById("studentLname").value;
    let birthdate = document.getElementById("studentDOB").value;
    let class_id = document.getElementById("studentClassSelect").value;
    let sex = document.getElementById("studentSex").value;
    let Pfname = document.getElementById("parentFname").value;
    let Plname = document.getElementById("parentLname").value;
    let Phone = document.getElementById("parentPhone").value;
    let email = document.getElementById("parentEmail").value;

    if(!fname || !lname || !birthdate || !class_id || !sex || !Pfname || !Plname || !Phone || !email){
        alert("Please fill all required fields");
        return;
    }

    addStudent(fname, lname, birthdate, class_id,sex,Pfname,Plname,Phone,email);
});


async function populateClassSelect(id) {
    let classes = await getAllClasses();
    
    
    

    classes.forEach(cls => {
        let option = document.createElement('option');
        option.value = cls.id;
        option.innerText = cls.name;
        document.getElementById(id).appendChild(option);
    });

    return classes;

};

populateClassSelect("studentClassSelect");


async function populateParentSelect(id) {

    let parents = await getParents();
    
    parents.forEach(parent => {
        let option = document.createElement('option');
        option.value = parent.id;
        option.innerText = `${parent.fname} ${parent.lname}`;
        document.getElementById(id).appendChild(option);
    });

    return parents;
}

populateParentSelect("parentsSelect");

async function getParents(){
    let url = `../api/getParents.php`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();

        return data; 
    } catch (error) {
        console.error("Error fetching Parents:", error?.message || error);
    }
}

document.getElementById("parentsSelect").addEventListener('change', async ()=>{
    let parent_id = document.getElementById("parentsSelect").value;
    let url = `../api/getParent.php?id=${encodeURIComponent(parent_id)}`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();
        document.getElementById("parentFnameAdmin").value = data.fname;
        document.getElementById("parentLnameAdmin").value = data.lname;
        document.getElementById("parentPhoneAdmin").value = data.phone;
        document.getElementById("parentEmailAdmin").value = data.email;
        

    } catch (error) {
        console.error("Error fetching Children:", error?.message || error);
    }
});


async function updateParent(id,fname,lname,phone,email){
    let url = "../api/editParent.php";
    try {
        let response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                id: id,
                parentFname: fname,
                parentLname: lname,
                parentPhone: phone,
                parentEmail: email
            })
        });
        
        
        let data = await response.json();
        

        if (data.success) {
            alert("تمت تحديث بيانات ولي الأمر بنجاح ✅");
        } else {
            alert("فشل في التحديث ❌: " + (data.error || "خطأ غير معروف"));
        }
    } catch (error) {
        console.error("خطأ أثناء تحديث بيانات ولي الأمر:", error);
    }
}

document.getElementById("add_parent").addEventListener('click',()=>{
    let id = document.getElementById("parentsSelect").value;
    let fname = document.getElementById("parentFnameAdmin").value;
    let lname = document.getElementById("parentLnameAdmin").value;
    let phone = document.getElementById("parentPhoneAdmin").value;
    let email = document.getElementById("parentEmailAdmin").value;

    if(!id || !fname || !lname || !phone || !email){
        alert("Please fill all required fields");
        return;
    }

    updateParent(id,fname,lname,phone,email);
});

document.getElementById("classDelete").addEventListener('click',()=>{
    let class_id = document.getElementById("deleteClassSelect").value;
    if(!class_id){
        alert("Please select a class to delete");
        return;
    }
    if(!confirm("Are you sure you want to delete this class? This action cannot be undone.")){
        return;
    }
    deleteClass(class_id);
    console.log(class_id);
    
});


async function deleteClass(class_id){
    let url = `../api/deleteClass.php?id=${encodeURIComponent(class_id)}`;
    try{
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        let data = await response.text();
        console.log(data);
        
        if(data.error){
            alert("Error deleting class: " + data.error);
            return;
        }else{
            alert("Class deleted successfully!");
        }
    }catch (error) {
        console.error("Error deleting class:", error?.message || error);
        alert("Error deleting class:", error?.message || error);
    }
}
populateClassSelect("deleteClassSelect");
populateClassSelect("studentInfoSelectClass");

async function populateStudentInfoSelect(){
    let students = await getStudents("studentInfoSelectClass");
    students.forEach(stud => {
        let option = document.createElement('option');
        option.value = stud.id;
        option.innerText = `${stud.fname} ${stud.lname}`;
        document.querySelector("#studentInfoSelectStudent").appendChild(option);
    });
}

document.getElementById("studentInfoSelectClass").addEventListener('change', async ()=>{
    document.getElementById("studentInfoSelectStudent").innerHTML = '<option value="0" selected disabled>اختر الطالب</option>';
    await populateStudentInfoSelect();
});

async function showStudentInfo(){
    let student_id = document.getElementById("studentInfoSelectStudent").value;
    let url = `../api/getStudentInfo.php?id=${encodeURIComponent(student_id)}`;
    let data;
    
    try {
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        data = await response.json();   
        if(document.getElementById("showSexOption")){
            document.getElementById("showSexOption").remove();
        }
        document.getElementById("showFname").value = data.fname;
        document.getElementById("showLname").value = data.lname;
        document.getElementById("showDOB").value = data.birth_date;
        document.getElementById("showParentFname").value = data.parent_fname;
        document.getElementById("showParentLname").value = data.parent_lname;
        document.getElementById("showParentPhone").value = data.parent_phone;
        document.getElementById("showParentEmail").value = data.email;
        if(data.sex == 'ذكر'){
            document.getElementById("showSexMale").selected = true;
        }else{
            document.getElementById("showSexFemale").selected = true;
        }
    } catch (error) {
        console.error("Error fetching Student Info:", error?.message || error);
    }
}

document.getElementById("studentInfoSelectStudent").addEventListener('change',async ()=>{
    await showStudentInfo();
});


populateClassSelect("ClassSelectStudentDelete");

async function populateStudentDeleteSelect(){
    let students = await getStudents("ClassSelectStudentDelete");
    students.forEach(stud => {
        let option = document.createElement('option');
        option.value = stud.id;
        option.innerText = `${stud.fname} ${stud.lname}`;
        document.querySelector("#StudentSelectStudentDelete").appendChild(option);
    });
}

document.getElementById("ClassSelectStudentDelete").addEventListener('change', async ()=>{
    document.getElementById("StudentSelectStudentDelete").innerHTML = '<option value="0" selected disabled>اختر الطالب</option>';
    await populateStudentDeleteSelect();
});

document.getElementById("deleteStudentBtn").addEventListener('click',async ()=>{
    let student_id = document.getElementById("StudentSelectStudentDelete").value;
    if(!student_id){
        alert("Please select a student to delete");
        return;
    }
    if(!confirm("Are you sure you want to delete this student? This action cannot be undone.")){
        return;
    }
    await deleteStudentApi(student_id);
    document.getElementById("StudentSelectStudentDelete").innerHTML = '<option value="0" selected disabled>اختر الطالب</option>';
    await populateStudentInfoSelect();
});

async function deleteStudentApi(student_id){
    let url = `../api/deleteStudent.php?id=${encodeURIComponent(student_id)}`;
    try{
        let response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        let data = await response.json();      
        
        if(data.error){
            alert("Error deleting student: " + data.error);
            return;
        }
        alert("Student deleted successfully!");
    }catch (error) {
        console.error("Error deleting student:", error?.message || error);
        alert("Error deleting student:", error?.message || error);
    }
}

let StudentAddSection = document.getElementById("studentAddSection");
let StudentDellSection = document.getElementById("studentDeleteSection");
let StudentInfoSection = document.getElementById("studentInfoSection");

document.getElementById("addStudentTab").addEventListener('click',()=>{
    StudentAddSection.classList.remove('hidden');
    StudentDellSection.classList.add('hidden');
    StudentInfoSection.classList.add('hidden');
});

document.getElementById("deleteStudentTab").addEventListener('click',()=>{
    StudentAddSection.classList.add('hidden');
    StudentDellSection.classList.remove('hidden');
    StudentInfoSection.classList.add('hidden');
});

document.getElementById("studentInfoTab").addEventListener('click',()=>{
    StudentAddSection.classList.add('hidden');
    StudentDellSection.classList.add('hidden');
    StudentInfoSection.classList.remove('hidden');
});


document.getElementById("saveStudentInfoBtn").addEventListener('click', async ()=>{
    let student_id = document.getElementById("studentInfoSelectStudent").value;
    let fname = document.getElementById("showFname").value;
    let lname = document.getElementById("showLname").value;
    let birthdate = document.getElementById("showDOB").value;
    let sex = document.getElementById("showSex").value;
    

    if(!student_id || !fname || !lname || !birthdate || !sex){
        alert("Please fill all required fields");
        return;
    }

    await updateStudentInfoApi(student_id, fname, lname, birthdate, sex);
    await showStudentInfo();
});


async function updateStudentInfoApi(student_id, fname, lname, birthdate, sex){
    let url = "../api/editStudent.php";
    try {
        let response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                id: student_id,
                Fname: fname,
                Lname: lname,
                bd: birthdate,
                sex: sex,
            })
        });
        
        
        let data = await response.json();
        

        if (data.success) {
            alert("تم تحديث بيانات الطالب بنجاح ✅");
        } else {
            alert("فشل في التحديث ❌: " + (data.error || "خطأ غير معروف"));
        }
    } catch (error) {
        console.error("خطأ أثناء تحديث بيانات الطالب:", error);
    }
}