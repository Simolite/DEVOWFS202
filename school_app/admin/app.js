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

document.querySelector("#mark").addEventListener('click',()=>{
    toggleSection('mark');
});

document.querySelector("#student").addEventListener('click',()=>{
    toggleSection('student');
});

document.querySelector("#teacher").addEventListener('click',()=>{
    toggleSection('teacher');
});

document.querySelector("#term").addEventListener('click',()=>{
    toggleSection('term');
});

document.querySelector("#messages").addEventListener('click',()=>{
    toggleSection('messages');
});

document.querySelector("#parents").addEventListener('click',()=>{
    toggleSection('parents');
});

document.querySelector("#classes").addEventListener('click',()=>{
    toggleSection('classes');
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


async function fetchOptions(apiUrl, select, labelKey = 'name') {
    try {
        const res = await fetch(apiUrl, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.innerText = item[labelKey];
            select.appendChild(opt);
        });
    } catch (err) {
        console.error("Error fetching options:", err);
    }
}

targetSelect.addEventListener('change', async () => {
    clearDynamicSelects();
    const value = targetSelect.value;

    if (value === "teachers") {
        const teacherSelect = createSelect('teacherSelect', 'اختر الأستاذ');
        container.appendChild(teacherSelect);
        await fetchOptions('../api/getTeachers.php', teacherSelect,'lname');
    }

    if (value === "classes") {
        const classSelect = createSelect('classSelect', 'اختر القسم');
        container.appendChild(classSelect);
        await fetchOptions('../api/getClasses.php', classSelect);
    }

    if (value === "students") {
        const classSelect = createSelect('studentClassSelect', 'اختر القسم');
        container.appendChild(classSelect);
        await fetchOptions('../api/getClasses.php', classSelect);

        const studentSelect = createSelect('studentSelect', 'اختر الطالب');
        container.appendChild(studentSelect);

        classSelect.addEventListener('change', async () => {
            studentSelect.innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.value = "0";
            defaultOption.selected = true;
            defaultOption.disabled = true;
            defaultOption.innerText = "اختر الطالب";
            studentSelect.appendChild(defaultOption);

            await fetchOptions(`../api/getClassStudents.php?class_id=${classSelect.value}`, studentSelect,'lname');
        });
    }
});


async function addAnnouncement(title, body, audience, id) {
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
                audience: audience,
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
    if(audience == 'all'){
        id=1;
    }else if(audience=='admins'){
        id = 1;
    }else if(audience=='teachers'){
        id = document.getElementById('teacherSelect').value;
    }else if(audience=='classes'){
        id = document.getElementById('classSelect').value;
    }else if(audience=='students'){
        id = document.getElementById('studentSelect').value;
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

    let url = `../api/getTeacherClasses.php?teacher_id=true`;
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
    if(dest == 'Attclass'||dest == 'AttclassDell'){
        return data;
    }
    data.forEach(student => {
        let option = document.createElement('option');
        option.value = student.id;
        option.innerText = `${student.fname} ${student.lname}`;
        document.querySelector("#student").appendChild(option);
    });    
}

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


async function getSubjets(dest) {

    let url = `../api/getTeacherSubjets.php?teacher_id=true`;
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
        dellAttendanceApi(att.value); // ✅ call the API function
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
