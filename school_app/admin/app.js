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
    let list = document.querySelector("select#account");
    accounts.forEach(account=>{
        let option = document.createElement("option");
        option.innerText = account.username;
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

document.querySelector("#announcement").addEventListener('click',()=>{
    toggleSection('announcement');
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
    getAccounts();
});


getAnnouncements();

const targetSelect = document.getElementById('target');
const container = document.getElementById('dynamicContainer');

// Clear dynamic selects
function clearDynamicSelects() {
    container.innerHTML = '';
}

// Create styled select
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


// Fetch options from API
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

// Handle target change
targetSelect.addEventListener('change', async () => {
    clearDynamicSelects();
    const value = targetSelect.value;

    if (value === "teachers") {
        const teacherSelect = createSelect('teacherSelect', 'اختر الأستاذ');
        container.appendChild(teacherSelect);
        await fetchOptions('../api/getTeachers.php', teacherSelect);
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

            await fetchOptions(`../api/getStudents.php?class_id=${classSelect.value}`, studentSelect);
        });
    }
});
