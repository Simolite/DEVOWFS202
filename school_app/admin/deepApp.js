/**
 * ============================================
 * ADMIN PANEL - SCHOOL MANAGEMENT SYSTEM
 * ============================================
 * 
 * Main improvements:
 * 1. Modular structure with clear separation
 * 2. Better error handling
 * 3. DRY principle implementation
 * 4. Consistent naming conventions
 * 5. Enhanced code organization
 */

// ============================================
// API SERVICE LAYER
// ============================================

class ApiService {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl || '../api/';
    }

    async fetchData(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Accept': 'application/json',
                ...options.headers
            }
        };

        const url = endpoint.startsWith('http') ? endpoint : `${this.baseUrl}${endpoint}`;
        
        try {
            const response = await fetch(url, { ...defaultOptions, ...options });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error(`API Error (${endpoint}):`, error.message);
            throw error;
        }
    }

    async postData(endpoint, data = {}) {
        return this.fetchData(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
    }

    async postFormData(endpoint, formData) {
        return this.fetchData(endpoint, {
            method: 'POST',
            body: formData
        });
    }

    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.fetchData(url);
    }
}

const api = new ApiService();

// ============================================
// DOM UTILITIES
// ============================================

class DOMUtils {
    static createElement(tag, attributes = {}, children = []) {
        const element = document.createElement(tag);
        
        Object.entries(attributes).forEach(([key, value]) => {
            if (key === 'className') {
                element.className = value;
            } else if (key === 'textContent') {
                element.textContent = value;
            } else if (key === 'innerText') {
                element.innerText = value;
            } else if (key.startsWith('on')) {
                element.addEventListener(key.substring(2).toLowerCase(), value);
            } else {
                element.setAttribute(key, value);
            }
        });

        children.forEach(child => {
            if (typeof child === 'string') {
                element.appendChild(document.createTextNode(child));
            } else if (child instanceof Node) {
                element.appendChild(child);
            }
        });

        return element;
    }

    static clearElement(element) {
        if (element) {
            element.innerHTML = '';
        }
    }

    static showAlert(message, type = 'info') {
        alert(message); // Consider using a better notification system
    }

    static confirmAction(message) {
        return confirm(message);
    }
}

// ============================================
// DATA MODELS
// ============================================

class Announcement {
    constructor({ id, title, body, created_at }) {
        this.id = id;
        this.title = title;
        this.body = body;
        this.created_at = created_at;
    }

    createRowElement(onDelete) {
        const tr = DOMUtils.createElement('tr');
        const cells = [
            DOMUtils.createElement('td', { textContent: this.title }),
            DOMUtils.createElement('td', { textContent: this.body }),
            DOMUtils.createElement('td', { textContent: this.created_at }),
            this.createDeleteCell(onDelete)
        ];

        cells.forEach(cell => tr.appendChild(cell));
        return tr;
    }

    createDeleteCell(onDelete) {
        const td = DOMUtils.createElement('td');
        const deleteBtn = DOMUtils.createElement('button', {
            textContent: 'حذف',
            className: 'bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition-colors',
            onclick: async () => {
                if (DOMUtils.confirmAction('هل أنت متأكد من حذف هذا الإعلان؟')) {
                    await onDelete(this.id);
                }
            }
        });
        td.appendChild(deleteBtn);
        return td;
    }
}

// ============================================
// APPLICATION MODULES
// ============================================

class AnnouncementsManager {
    constructor() {
        this.container = document.querySelector('#notifaction_section tbody');
    }

    async initialize() {
        await this.loadAnnouncements();
        this.setupEventListeners();
    }

    async loadAnnouncements() {
        try {
            DOMUtils.clearElement(this.container);
            const data = await api.get('getAnnouncements.php', { audience: 'all', class_id: 'all' });
            this.renderAnnouncements(data);
        } catch (error) {
            console.error('Failed to load announcements:', error);
            DOMUtils.showAlert('فشل في تحميل الإعلانات', 'error');
        }
    }

    renderAnnouncements(announcements) {
        announcements.forEach(announcement => {
            const ann = new Announcement(announcement);
            this.container.appendChild(ann.createRowElement(async (id) => {
                await this.deleteAnnouncement(id, ann);
            }));
        });
    }

    async deleteAnnouncement(id, announcementRow) {
        try {
            const result = await api.get(`deleteAnnouncement.php?id=${id}`);
            if (result.status === 200) {
                announcementRow.remove();
                DOMUtils.showAlert('تم حذف الإعلان بنجاح', 'success');
            } else {
                DOMUtils.showAlert(result.error || 'حدث خطأ أثناء الحذف', 'error');
            }
        } catch (error) {
            console.error('Delete failed:', error);
            DOMUtils.showAlert('فشل في حذف الإعلان', 'error');
        }
    }

    async addAnnouncement(title, body, audience, targetId) {
        try {
            const data = {
                title,
                body,
                audience,
                id: targetId
            };

            const result = await api.postData('addAnnouncement.php', data);
            
            if (result.success) {
                DOMUtils.showAlert('تمت إضافة الإعلان بنجاح ✅', 'success');
                await this.loadAnnouncements();
            } else {
                DOMUtils.showAlert(`فشل في الإضافة: ${result.error || 'خطأ غير معروف'}`, 'error');
            }
        } catch (error) {
            console.error('Add announcement failed:', error);
            DOMUtils.showAlert('خطأ أثناء إرسال الإعلان', 'error');
        }
    }

    setupEventListeners() {
        const addButton = document.getElementById('add_ann');
        if (addButton) {
            addButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleAddAnnouncement();
            });
        }
    }

    handleAddAnnouncement() {
        const title = document.getElementById('notifTitle').value;
        const body = document.getElementById('notifBody').value;
        const audience = document.getElementById('target').value;
        let targetId = '';

        if (audience === 'classes') {
            targetId = document.getElementById('classSelect').value;
        }

        if (!title || !body) {
            DOMUtils.showAlert('يرجى ملء جميع الحقول المطلوبة', 'warning');
            return;
        }

        this.addAnnouncement(title, body, audience, targetId);
    }
}

// ============================================
// ACCOUNTS MANAGER
// ============================================

class AccountsManager {
    constructor() {
        this.selectElement = document.querySelector('#accountSelect');
        this.roleSelect = document.querySelector('#accRole');
        this.passwordInput = document.querySelector('#password');
        this.applyButton = document.querySelector('#applyBtn');
    }

    async initialize() {
        await this.loadAccounts();
        this.setupEventListeners();
    }

    async loadAccounts() {
        const role = this.roleSelect.value;
        try {
            const accounts = await api.get('getAccount.php', { role });
            this.renderAccounts(accounts);
        } catch (error) {
            console.error('Failed to load accounts:', error);
        }
    }

    renderAccounts(accounts) {
        DOMUtils.clearElement(this.selectElement);
        
        accounts.forEach(account => {
            const option = DOMUtils.createElement('option', {
                value: account.id,
                textContent: account.username
            });
            this.selectElement.appendChild(option);
        });
    }

    async changePassword(accountId, newPassword) {
        try {
            const result = await api.postData('changePassword.php', {
                id: accountId,
                pass: newPassword
            });

            if (result.success) {
                DOMUtils.showAlert('تم تغيير كلمة المرور بنجاح ✅', 'success');
                this.passwordInput.value = '';
            } else {
                DOMUtils.showAlert(`فشل في التغيير: ${result.error || 'خطأ غير معروف'}`, 'error');
            }
        } catch (error) {
            console.error('Password change failed:', error);
            DOMUtils.showAlert('خطأ أثناء تغيير كلمة المرور', 'error');
        }
    }

    setupEventListeners() {
        this.roleSelect.addEventListener('change', () => this.loadAccounts());
        
        this.applyButton.addEventListener('click', () => {
            const accountId = this.selectElement.value;
            const newPassword = this.passwordInput.value;

            if (!accountId) {
                DOMUtils.showAlert('يرجى اختيار حساب', 'warning');
                return;
            }

            if (!newPassword) {
                DOMUtils.showAlert('يرجى إدخال كلمة المرور الجديدة', 'warning');
                return;
            }

            if (DOMUtils.confirmAction('هل أنت متأكد من تغيير كلمة المرور؟')) {
                this.changePassword(accountId, newPassword);
            }
        });
    }
}

// ============================================
// ATTENDANCE MANAGER
// ============================================

class AttendanceManager {
    constructor() {
        this.classSelect = document.querySelector('#Attclass');
        this.subjectSelect = document.querySelector('#Attsub');
        this.dateInput = document.querySelector('#Attdate');
        this.getListButton = document.querySelector('#getAttList');
        this.submitButton = document.querySelector('#submitAtt');
    }

    async initialize() {
        await this.loadClasses();
        this.setupEventListeners();
    }

    async loadClasses() {
        try {
            const classes = await api.get('getClasses.php');
            this.renderClassOptions(classes);
        } catch (error) {
            console.error('Failed to load classes:', error);
        }
    }

    renderClassOptions(classes) {
        classes.forEach(cls => {
            const option = DOMUtils.createElement('option', {
                value: cls.id,
                textContent: cls.name
            });
            this.classSelect.appendChild(option);
        });
    }

    async loadSubjects() {
        try {
            const subjects = await api.get('getTeacherSubjets.php', { teacher_id: 'all' });
            this.renderSubjectOptions(subjects);
        } catch (error) {
            console.error('Failed to load subjects:', error);
        }
    }

    renderSubjectOptions(subjects) {
        DOMUtils.clearElement(this.subjectSelect);
        
        const defaultOption = DOMUtils.createElement('option', {
            value: '0',
            textContent: 'اختر المادة',
            disabled: true,
            selected: true
        });
        this.subjectSelect.appendChild(defaultOption);

        subjects.forEach(subject => {
            const option = DOMUtils.createElement('option', {
                value: subject.id,
                textContent: subject.name
            });
            this.subjectSelect.appendChild(option);
        });
    }

    async loadStudentsForAttendance() {
        const classId = this.classSelect.value;
        if (!classId || classId === '0') return [];

        try {
            return await api.get('getClassStudents.php', { class_id: classId });
        } catch (error) {
            console.error('Failed to load students:', error);
            return [];
        }
    }

    async renderAttendanceList() {
        const classId = this.classSelect.value;
        const subjectId = this.subjectSelect.value;

        if (classId === '0' || subjectId === '0') {
            DOMUtils.showAlert('يرجى اختيار القسم والمادة', 'warning');
            return;
        }

        const students = await this.loadStudentsForAttendance();
        const tbody = document.querySelector('#attendance_section tbody') || 
                      DOMUtils.createElement('tbody', { id: 'attendanceList' });

        DOMUtils.clearElement(tbody);

        students.forEach(student => {
            const tr = DOMUtils.createElement('tr');
            const nameCell = DOMUtils.createElement('td', {
                textContent: `${student.fname} ${student.lname}`
            });

            const checkboxCell = DOMUtils.createElement('td');
            const checkbox = DOMUtils.createElement('input', {
                type: 'checkbox',
                value: student.id,
                className: 'studentBox mr-2'
            });

            checkboxCell.appendChild(checkbox);
            tr.appendChild(nameCell);
            tr.appendChild(checkboxCell);
            tbody.appendChild(tr);
        });

        const table = document.querySelector('#attendance_section table');
        if (table.querySelector('tbody')) {
            table.querySelector('tbody').remove();
        }
        table.appendChild(tbody);
    }

    async submitAttendance() {
        const date = this.dateInput.value;
        if (!date) {
            DOMUtils.showAlert('يرجى اختيار تاريخ', 'warning');
            return;
        }

        const classId = this.classSelect.value;
        const subjectId = this.subjectSelect.value;
        const checkboxes = document.querySelectorAll('.studentBox');

        const absentIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        const presentIds = Array.from(checkboxes)
            .filter(cb => !cb.checked)
            .map(cb => cb.value);

        // Submit absent students
        const absentPromises = absentIds.map(studentId => 
            api.postFormData('addAtt.php', new URLSearchParams({
                student_id: studentId,
                subject_id: subjectId,
                date: date,
                stat: 'absent'
            }))
        );

        // Submit present students
        const presentPromises = presentIds.map(studentId =>
            api.postFormData('addAtt.php', new URLSearchParams({
                student_id: studentId,
                subject_id: subjectId,
                date: date,
                stat: 'present'
            }))
        );

        try {
            await Promise.all([...absentPromises, ...presentPromises]);
            DOMUtils.showAlert('تم تسجيل الحضور بنجاح', 'success');
            document.querySelector('#attendanceList')?.remove();
        } catch (error) {
            console.error('Failed to submit attendance:', error);
            DOMUtils.showAlert('فشل في تسجيل الحضور', 'error');
        }
    }

    setupEventListeners() {
        this.classSelect.addEventListener('change', async () => {
            await this.loadSubjects();
        });

        this.getListButton.addEventListener('click', async () => {
            await this.renderAttendanceList();
        });

        this.submitButton.addEventListener('click', async () => {
            await this.submitAttendance();
        });
    }
}

// ============================================
// UI STATE MANAGER
// ============================================

class UIManager {
    constructor() {
        this.sections = {
            notifaction: document.getElementById('notifaction_section'),
            account: document.getElementById('account_section'),
            attendance: document.getElementById('attendance_section'),
            class: document.getElementById('class_section'),
            marks: document.getElementById('marks_section'),
            student: document.getElementById('student_section'),
            messages: document.getElementById('messages_section'),
            parents: document.getElementById('parents_section')
        };

        this.navItems = {
            notifaction: document.querySelector('#notifaction'),
            account: document.querySelector('#account'),
            attendance: document.querySelector('#attendance'),
            class: document.querySelector('#class'),
            marks: document.querySelector('#marks'),
            student: document.querySelector('#student'),
            messages: document.querySelector('#messages'),
            parents: document.querySelector('#parents')
        };
    }

    initialize() {
        this.setupNavigation();
        this.activateSection('notifaction');
    }

    setupNavigation() {
        Object.entries(this.navItems).forEach(([sectionId, navItem]) => {
            if (navItem) {
                navItem.addEventListener('click', () => this.activateSection(sectionId));
            }
        });
    }

    activateSection(sectionId) {
        // Deactivate all sections
        Object.values(this.sections).forEach(section => {
            if (section) section.classList.add('hidden');
        });

        Object.values(this.navItems).forEach(navItem => {
            if (navItem) {
                navItem.classList.remove('selected');
                navItem.classList.add('text-gray-600');
                navItem.classList.remove('text-blue-600', 'font-semibold');
            }
        });

        // Activate selected section
        const selectedSection = this.sections[sectionId];
        const selectedNavItem = this.navItems[sectionId];

        if (selectedSection) {
            selectedSection.classList.remove('hidden');
        }

        if (selectedNavItem) {
            selectedNavItem.classList.add('selected', 'text-blue-600', 'font-semibold');
            selectedNavItem.classList.remove('text-gray-600');
        }
    }
}

// ============================================
// DYNAMIC FORM MANAGER
// ============================================

class DynamicFormManager {
    constructor() {
        this.targetSelect = document.getElementById('target');
        this.container = document.getElementById('dynamicContainer');
        this.recipientSelect = document.getElementById('recipient');
        this.dynamicRecipientContainer = document.getElementById('dynamicRecipientContainer') || 
                                        this.createDynamicContainer();
    }

    createDynamicContainer() {
        const container = DOMUtils.createElement('div', { id: 'dynamicRecipientContainer' });
        const form = document.getElementById('messageForm');
        if (form) {
            form.insertBefore(container, form.children[1]);
        }
        return container;
    }

    initialize() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        if (this.targetSelect) {
            this.targetSelect.addEventListener('change', () => this.handleTargetChange());
        }

        if (this.recipientSelect) {
            this.recipientSelect.addEventListener('change', () => this.handleRecipientChange());
        }
    }

    clearContainer(container) {
        if (container) {
            container.innerHTML = '';
        }
    }

    createSelect(id, placeholder, options = {}) {
        const select = DOMUtils.createElement('select', {
            id,
            className: `dynamic-select w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium 
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                       hover:border-blue-400 transition-colors mt-2 ${options.className || ''}`
        });

        const defaultOption = DOMUtils.createElement('option', {
            value: '0',
            textContent: placeholder,
            disabled: true,
            selected: true
        });

        select.appendChild(defaultOption);
        return select;
    }

    async handleTargetChange() {
        this.clearContainer(this.container);
        const value = this.targetSelect.value;

        if (value === 'classes') {
            await this.createClassSelect();
        }
    }

    async createClassSelect() {
        const classSelect = this.createSelect('classSelect', 'اختر القسم');
        this.container.appendChild(classSelect);
        
        try {
            const classes = await api.get('getClasses.php');
            classes.forEach(cls => {
                const option = DOMUtils.createElement('option', {
                    value: cls.id,
                    textContent: cls.name
                });
                classSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load classes:', error);
        }
    }

    async handleRecipientChange() {
        this.clearContainer(this.dynamicRecipientContainer);
        const value = this.recipientSelect.value;

        if (value === 'student') {
            await this.createStudentRecipientFields();
        } else if (value === 'teacher') {
            await this.createTeacherRecipientField();
        }
    }

    async createStudentRecipientFields() {
        const classSelect = this.createSelect('msgClassSelect', 'اختر القسم');
        this.dynamicRecipientContainer.appendChild(classSelect);

        const studentSelect = this.createSelect('msgStudentSelect', 'اختر الطالب');
        this.dynamicRecipientContainer.appendChild(studentSelect);

        await this.populateClasses(classSelect);

        classSelect.addEventListener('change', async () => {
            await this.populateStudents(studentSelect, classSelect.value);
        });
    }

    async createTeacherRecipientField() {
        const teacherSelect = this.createSelect('msgTeacherSelect', 'اختر الأستاذ');
        this.dynamicRecipientContainer.appendChild(teacherSelect);
        await this.populateTeachers(teacherSelect);
    }

    async populateClasses(selectElement) {
        try {
            const classes = await api.get('getClasses.php');
            classes.forEach(cls => {
                const option = DOMUtils.createElement('option', {
                    value: cls.id,
                    textContent: cls.name
                });
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load classes:', error);
        }
    }

    async populateStudents(selectElement, classId) {
        DOMUtils.clearElement(selectElement);
        const defaultOption = this.createSelect('', 'اختر الطالب');
        selectElement.appendChild(defaultOption);

        try {
            const students = await api.get('getClassStudents.php', { class_id: classId });
            students.forEach(student => {
                const option = DOMUtils.createElement('option', {
                    value: student.id,
                    textContent: `${student.fname} ${student.lname}`
                });
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load students:', error);
        }
    }

    async populateTeachers(selectElement) {
        try {
            const teachers = await api.get('getTeachers.php');
            teachers.forEach(teacher => {
                const option = DOMUtils.createElement('option', {
                    value: teacher.id,
                    textContent: `${teacher.fname} ${teacher.lname}`
                });
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load teachers:', error);
        }
    }
}

// ============================================
// MESSAGES MANAGER
// ============================================

class MessagesManager {
    constructor() {
        this.container = document.getElementById('messagesList');
        this.sendButton = document.getElementById('message_send_btn');
    }

    async initialize() {
        await this.loadMessages();
        this.setupEventListeners();
    }

    async loadMessages() {
        try {
            const messages = await api.get('getMessages.php');
            this.renderMessages(messages);
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    }

    renderMessages(messages) {
        DOMUtils.clearElement(this.container);
        
        messages.forEach(message => {
            const tr = DOMUtils.createElement('tr', { className: 'border-b' });
            const cells = [
                DOMUtils.createElement('td', { 
                    textContent: `${message.sender_name} (${message.sender_role})`,
                    className: 'p-2'
                }),
                DOMUtils.createElement('td', { 
                    textContent: message.title,
                    className: 'p-2'
                }),
                DOMUtils.createElement('td', { 
                    textContent: message.message,
                    className: 'p-2'
                }),
                DOMUtils.createElement('td', { 
                    textContent: message.type,
                    className: 'p-2'
                }),
                DOMUtils.createElement('td', { 
                    textContent: message.sent_at,
                    className: 'p-2'
                })
            ];

            cells.forEach(cell => tr.appendChild(cell));
            this.container.appendChild(tr);
        });
    }

    async sendMessage() {
        const receiverRole = document.getElementById('recipient').value;
        let receiverId;

        switch (receiverRole) {
            case 'admin':
                receiverId = 1;
                break;
            case 'teacher':
                receiverId = document.getElementById('msgTeacherSelect')?.value;
                break;
            case 'student':
                receiverId = document.getElementById('msgStudentSelect')?.value;
                break;
        }

        const message = document.getElementById('messageContent').value.trim();
        const title = document.getElementById('message_subject').value.trim();
        const type = document.getElementById('messageType').value;

        if (!message || !title) {
            DOMUtils.showAlert('يرجى ملء جميع الحقول المطلوبة', 'warning');
            return;
        }

        if (!receiverId || receiverId === '0') {
            DOMUtils.showAlert('يرجى اختيار المستلم', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('receiver_role', receiverRole);
        formData.append('message', message);
        formData.append('title', title);
        formData.append('type', type);

        try {
            const result = await api.postFormData('sendMessages.php', formData);
            
            if (result.success) {
                DOMUtils.showAlert('تم إرسال الرسالة بنجاح', 'success');
                document.getElementById('messageContent').value = '';
                document.getElementById('message_subject').value = '';
                await this.loadMessages();
            } else {
                DOMUtils.showAlert(`خطأ: ${result.error}`, 'error');
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            DOMUtils.showAlert('حدث خطأ أثناء إرسال الرسالة', 'error');
        }
    }

    setupEventListeners() {
        if (this.sendButton) {
            this.sendButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }
    }
}

// ============================================
// MARKS MANAGER
// ============================================

class MarksManager {
    constructor() {
        this.classSelect = document.querySelector('#Markclass');
        this.subjectSelect = document.querySelector('#Marksubject');
        this.studentSelect = document.querySelector('#student_mark_select');
        this.termSelect = document.querySelector('#term_mark_select');
        this.markInput = document.querySelector('#markToSubmit');
        this.dateInput = document.querySelector('#Markdate');
        this.submitButton = document.querySelector('#SubmitMark');
        this.showButton = document.querySelector('#Markshow');
        this.marksList = document.querySelector('#marksList');
    }

    async initialize() {
        await this.loadInitialData();
        this.setupEventListeners();
    }

    async loadInitialData() {
        await this.loadClasses();
        await this.loadTerms();
    }

    async loadClasses() {
        try {
            const classes = await api.get('getClasses.php');
            this.renderClassOptions(classes);
        } catch (error) {
            console.error('Failed to load classes:', error);
        }
    }

    renderClassOptions(classes) {
        classes.forEach(cls => {
            const option = DOMUtils.createElement('option', {
                value: cls.id,
                textContent: cls.name
            });
            this.classSelect.appendChild(option);
        });
    }

    async loadSubjects() {
        try {
            const subjects = await api.get('getTeacherSubjets.php', { teacher_id: 'all' });
            this.renderSubjectOptions(subjects);
        } catch (error) {
            console.error('Failed to load subjects:', error);
        }
    }

    renderSubjectOptions(subjects) {
        DOMUtils.clearElement(this.subjectSelect);
        
        const defaultOption = DOMUtils.createElement('option', {
            value: '0',
            textContent: 'اختر المادة',
            disabled: true,
            selected: true
        });
        this.subjectSelect.appendChild(defaultOption);

        subjects.forEach(subject => {
            const option = DOMUtils.createElement('option', {
                value: subject.id,
                textContent: subject.name
            });
            this.subjectSelect.appendChild(option);
        });
    }

    async loadStudents(classId) {
        try {
            const students = await api.get('getClassStudents.php', { class_id: classId });
            this.renderStudentOptions(students);
        } catch (error) {
            console.error('Failed to load students:', error);
        }
    }

    renderStudentOptions(students) {
        DOMUtils.clearElement(this.studentSelect);
        
        const defaultOption = DOMUtils.createElement('option', {
            value: '0',
            textContent: 'اختر الطالب',
            disabled: true,
            selected: true
        });
        this.studentSelect.appendChild(defaultOption);

        students.forEach(student => {
            const option = DOMUtils.createElement('option', {
                value: student.id,
                textContent: `${student.fname} ${student.lname}`
            });
            this.studentSelect.appendChild(option);
        });
    }

    async loadTerms() {
        try {
            const terms = await api.get('get_terms.php');
            this.renderTermOptions(terms);
        } catch (error) {
            console.error('Failed to load terms:', error);
        }
    }

    renderTermOptions(terms) {
        terms.forEach(term => {
            const option = DOMUtils.createElement('option', {
                value: term.id,
                textContent: term.name
            });
            this.termSelect.appendChild(option);
        });
    }

    async addMark() {
        const studentId = this.studentSelect.value;
        const subjectId = this.subjectSelect.value;
        const mark = this.markInput.value;
        const term = this.termSelect.value;
        const date = this.dateInput.value;

        if (!studentId || !subjectId || !mark || !term || !date) {
            DOMUtils.showAlert('يرجى ملء جميع الحقول المطلوبة', 'warning');
            return;
        }

        try {
            const result = await api.postFormData('addMark.php', new URLSearchParams({
                student_id: studentId,
                subject_id: subjectId,
                mark: mark,
                term: term,
                date: date
            }));

            if (result.error) {
                DOMUtils.showAlert(`خطأ: ${result.error}`, 'error');
            } else {
                DOMUtils.showAlert('تم إضافة الدرجة بنجاح', 'success');
                this.markInput.value = '';
                await this.showMarks();
            }
        } catch (error) {
            console.error('Failed to add mark:', error);
            DOMUtils.showAlert('حدث خطأ أثناء إضافة الدرجة', 'error');
        }
    }

    async showMarks() {
        const studentId = this.studentSelect.value;
        const term = this.termSelect.value;

        if (!studentId || !term) {
            DOMUtils.showAlert('يرجى اختيار الطالب والفصل الدراسي', 'warning');
            return;
        }

        try {
            const marks = await api.get('get_marks.php', {
                student_id: studentId,
                term: term,
                sub: 'all'
            });

            this.renderMarks(marks);
        } catch (error) {
            console.error('Failed to load marks:', error);
        }
    }

    renderMarks(marks) {
        DOMUtils.clearElement(this.marksList);
        
        Object.entries(marks).forEach(([subject, subjectMarks]) => {
            subjectMarks.forEach(mark => {
                const tr = DOMUtils.createElement('tr', { className: 'border-b' });
                const cells = [
                    DOMUtils.createElement('td', {
                        textContent: subject,
                        className: 'p-2'
                    }),
                    DOMUtils.createElement('td', {
                        textContent: mark.mark,
                        className: 'p-2'
                    }),
                    DOMUtils.createElement('td', {
                        textContent: mark.exam_date,
                        className: 'p-2'
                    }),
                    this.createDeleteCell(mark.id)
                ];

                cells.forEach(cell => tr.appendChild(cell));
                this.marksList.appendChild(tr);
            });
        });
    }

    createDeleteCell(markId) {
        const td = DOMUtils.createElement('td', { className: 'p-2' });
        const deleteButton = DOMUtils.createElement('button', {
            textContent: 'حذف',
            className: 'bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors',
            onclick: async () => {
                if (DOMUtils.confirmAction('هل أنت متأكد من حذف هذه الدرجة؟')) {
                    await this.deleteMark(markId);
                }
            }
        });
        td.appendChild(deleteButton);
        return td;
    }

    async deleteMark(markId) {
        try {
            await api.get(`dellMark.php?mark_id=${markId}`);
            DOMUtils.showAlert('تم حذف الدرجة بنجاح', 'success');
            await this.showMarks();
        } catch (error) {
            console.error('Failed to delete mark:', error);
            DOMUtils.showAlert('فشل في حذف الدرجة', 'error');
        }
    }

    setupEventListeners() {
        this.classSelect.addEventListener('change', async () => {
            await this.loadSubjects();
            await this.loadStudents(this.classSelect.value);
        });

        this.submitButton.addEventListener('click', async (e) => {
            e.preventDefault();
            await this.addMark();
        });

        this.showButton.addEventListener('click', async (e) => {
            e.preventDefault();
            await this.showMarks();
        });
    }
}

// ============================================
// CLASSES MANAGER
// ============================================

class ClassesManager {
    constructor() {
        this.showCreate = document.getElementById('showCreate');
        this.showEdit = document.getElementById('showEdit');
        this.createSection = document.getElementById('createSection');
        this.editSection = document.getElementById('editSection');
        this.editClassSelect = document.getElementById('editClassSelect');
        this.classNameEdit = document.getElementById('classNameEdit');
        this.classTimeEdit = document.getElementById('classTimeEdit');
        this.addClassButton = document.getElementById('add_class');
        this.classSubmitButton = document.getElementById('classSubmit');
    }

    async initialize() {
        await this.loadClasses();
        this.setupEventListeners();
        this.activateTab('create');
    }

    async loadClasses() {
        try {
            const classes = await api.get('getClasses.php');
            this.renderClassOptions(classes);
            return classes;
        } catch (error) {
            console.error('Failed to load classes:', error);
            return [];
        }
    }

    renderClassOptions(classes) {
        DOMUtils.clearElement(this.editClassSelect);
        
        classes.forEach(cls => {
            const option = DOMUtils.createElement('option', {
                value: cls.id,
                textContent: cls.name
            });
            this.editClassSelect.appendChild(option);
        });
    }

    async createClass(className) {
        if (!className.trim()) {
            DOMUtils.showAlert('يرجى إدخال اسم القسم', 'warning');
            return;
        }

        try {
            const result = await api.get(`createClass.php?name=${encodeURIComponent(className)}`);
            
            if (result.error) {
                DOMUtils.showAlert(`خطأ في إنشاء القسم: ${result.error}`, 'error');
            } else {
                DOMUtils.showAlert('تم إنشاء القسم بنجاح', 'success');
                await this.loadClasses();
            }
        } catch (error) {
            console.error('Failed to create class:', error);
            DOMUtils.showAlert('حدث خطأ أثناء إنشاء القسم', 'error');
        }
    }

    async editClass(classId, className, timetableUrl) {
        if (!classId || !className || !timetableUrl) {
            DOMUtils.showAlert('يرجى ملء جميع الحقول المطلوبة', 'warning');
            return;
        }

        try {
            const result = await api.get(`editClass.php`, {
                id: classId,
                name: className,
                timetable_url: timetableUrl
            });

            if (result.error) {
                DOMUtils.showAlert(`خطأ في تعديل القسم: ${result.error}`, 'error');
            } else {
                DOMUtils.showAlert('تم تعديل القسم بنجاح', 'success');
            }
        } catch (error) {
            console.error('Failed to edit class:', error);
            DOMUtils.showAlert('حدث خطأ أثناء تعديل القسم', 'error');
        }
    }

    activateTab(tab) {
        if (tab === 'create') {
            this.createSection.classList.remove('hidden');
            this.editSection.classList.add('hidden');
            
            this.showCreate.classList.add('bg-gray-50', 'text-gray-800');
            this.showCreate.classList.remove('bg-gray-100', 'text-gray-600');
            
            this.showEdit.classList.add('bg-gray-100', 'text-gray-600');
            this.showEdit.classList.remove('bg-gray-50', 'text-gray-800');
        } else {
            this.editSection.classList.remove('hidden');
            this.createSection.classList.add('hidden');
            
            this.showEdit.classList.add('bg-gray-50', 'text-gray-800');
            this.showEdit.classList.remove('bg-gray-100', 'text-gray-600');
            
            this.showCreate.classList.add('bg-gray-100', 'text-gray-600');
            this.showCreate.classList.remove('bg-gray-50', 'text-gray-800');
        }
    }

    setupEventListeners() {
        this.showCreate.addEventListener('click', () => this.activateTab('create'));
        this.showEdit.addEventListener('click', () => this.activateTab('edit'));

        this.addClassButton.addEventListener('click', async () => {
            const className = document.querySelector('#add_class_input').value;
            if (DOMUtils.confirmAction('هل أنت متأكد من إضافة قسم جديد؟')) {
                await this.createClass(className);
                document.querySelector('#add_class_input').value = '';
            }
        });

        this.editClassSelect.addEventListener('change', async () => {
            const classId = this.editClassSelect.value;
            if (classId) {
                const classes = await this.loadClasses();
                const selectedClass = classes.find(cls => cls.id == classId);
                if (selectedClass) {
                    this.classNameEdit.value = selectedClass.name;
                    this.classTimeEdit.value = selectedClass.timetable_url || '';
                }
            }
        });

        this.classSubmitButton.addEventListener('click', async () => {
            const classId = this.editClassSelect.value;
            const className = this.classNameEdit.value;
            const timetableUrl = this.classTimeEdit.value;

            if (DOMUtils.confirmAction('هل أنت متأكد من تعديل بيانات القسم؟')) {
                await this.editClass(classId, className, timetableUrl);
            }
        });
    }
}

// ============================================
// STUDENTS MANAGER
// ============================================

class StudentsManager {
    constructor() {
        this.addStudentButton = document.getElementById('add_student');
        this.classSelect = document.getElementById('studentClassSelect');
    }

    async initialize() {
        await this.loadClasses();
        this.setupEventListeners();
    }

    async loadClasses() {
        try {
            const classes = await api.get('getClasses.php');
            this.renderClassOptions(classes);
        } catch (error) {
            console.error('Failed to load classes:', error);
        }
    }

    renderClassOptions(classes) {
        classes.forEach(cls => {
            const option = DOMUtils.createElement('option', {
                value: cls.id,
                textContent: cls.name
            });
            this.classSelect.appendChild(option);
        });
    }

    async addStudent(studentData) {
        try {
            const result = await api.postData('addStudent.php', studentData);
            
            if (result.success) {
                DOMUtils.showAlert('تمت إضافة الطالب بنجاح ✅', 'success');
                return true;
            } else {
                DOMUtils.showAlert(`فشل في الإضافة: ${result.error || 'خطأ غير معروف'}`, 'error');
                return false;
            }
        } catch (error) {
            console.error('Failed to add student:', error);
            DOMUtils.showAlert('حدث خطأ أثناء إضافة الطالب', 'error');
            return false;
        }
    }

    getStudentFormData() {
        return {
            fname: document.getElementById('studentFname').value.trim(),
            lname: document.getElementById('studentLname').value.trim(),
            birthdate: document.getElementById('studentDOB').value,
            class_id: document.getElementById('studentClassSelect').value,
            sex: document.getElementById('studentSex').value,
            pfname: document.getElementById('parentFname').value.trim(),
            plname: document.getElementById('parentLname').value.trim(),
            phone: document.getElementById('parentPhone').value.trim(),
            email: document.getElementById('parentEmail').value.trim()
        };
    }

    validateStudentData(data) {
        const requiredFields = [
            'fname', 'lname', 'birthdate', 'class_id', 'sex',
            'pfname', 'plname', 'phone', 'email'
        ];

        const missingFields = requiredFields.filter(field => !data[field]);
        
        if (missingFields.length > 0) {
            DOMUtils.showAlert('يرجى ملء جميع الحقول المطلوبة', 'warning');
            return false;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            DOMUtils.showAlert('يرجى إدخال بريد إلكتروني صحيح', 'warning');
            return false;
        }

        // Validate phone number (basic validation)
        const phoneRegex = /^[0-9]{10,15}$/;
        if (!phoneRegex.test(data.phone.replace(/\D/g, ''))) {
            DOMUtils.showAlert('يرجى إدخال رقم هاتف صحيح', 'warning');
            return false;
        }

        return true;
    }

    setupEventListeners() {
        this.addStudentButton.addEventListener('click', async () => {
            const studentData = this.getStudentFormData();
            
            if (this.validateStudentData(studentData)) {
                if (DOMUtils.confirmAction('هل أنت متأكد من إضافة الطالب؟')) {
                    const success = await this.addStudent(studentData);
                    if (success) {
                        // Clear form
                        Object.keys(studentData).forEach(key => {
                            const element = document.getElementById(`student${key.charAt(0).toUpperCase() + key.slice(1)}`);
                            if (element) element.value = '';
                        });
                    }
                }
            }
        });
    }
}

// ============================================
// PARENTS MANAGER
// ============================================

class ParentsManager {
    constructor() {
        this.parentsSelect = document.getElementById('parentsSelect');
        this.updateParentButton = document.getElementById('add_parent');
    }

    async initialize() {
        await this.loadParents();
        this.setupEventListeners();
    }

    async loadParents() {
        try {
            const parents = await api.get('getParents.php');
            this.renderParentOptions(parents);
            return parents;
        } catch (error) {
            console.error('Failed to load parents:', error);
            return [];
        }
    }

    renderParentOptions(parents) {
        DOMUtils.clearElement(this.parentsSelect);
        
        const defaultOption = DOMUtils.createElement('option', {
            value: '0',
            textContent: 'اختر ولي الأمر',
            disabled: true,
            selected: true
        });
        this.parentsSelect.appendChild(defaultOption);

        parents.forEach(parent => {
            const option = DOMUtils.createElement('option', {
                value: parent.id,
                textContent: `${parent.fname} ${parent.lname}`
            });
            this.parentsSelect.appendChild(option);
        });
    }

    async loadParentDetails(parentId) {
        try {
            const parent = await api.get(`getParent.php`, { id: parentId });
            
            if (parent) {
                document.getElementById('parentFnameAdmin').value = parent.fname || '';
                document.getElementById('parentLnameAdmin').value = parent.lname || '';
                document.getElementById('parentPhoneAdmin').value = parent.phone || '';
                document.getElementById('parentEmailAdmin').value = parent.email || '';
            }
        } catch (error) {
            console.error('Failed to load parent details:', error);
        }
    }

    async updateParent(parentData) {
        try {
            const result = await api.postData('editParent.php', parentData);
            
            if (result.success) {
                DOMUtils.showAlert('تم تحديث بيانات ولي الأمر بنجاح ✅', 'success');
                return true;
            } else {
                DOMUtils.showAlert(`فشل في التحديث: ${result.error || 'خطأ غير معروف'}`, 'error');
                return false;
            }
        } catch (error) {
            console.error('Failed to update parent:', error);
            DOMUtils.showAlert('حدث خطأ أثناء تحديث بيانات ولي الأمر', 'error');
            return false;
        }
    }

    getParentFormData() {
        return {
            id: this.parentsSelect.value,
            parentFname: document.getElementById('parentFnameAdmin').value.trim(),
            parentLname: document.getElementById('parentLnameAdmin').value.trim(),
            parentPhone: document.getElementById('parentPhoneAdmin').value.trim(),
            parentEmail: document.getElementById('parentEmailAdmin').value.trim()
        };
    }

    validateParentData(data) {
        if (!data.id || data.id === '0') {
            DOMUtils.showAlert('يرجى اختيار ولي الأمر', 'warning');
            return false;
        }

        const requiredFields = ['parentFname', 'parentLname', 'parentPhone', 'parentEmail'];
        const missingFields = requiredFields.filter(field => !data[field]);
        
        if (missingFields.length > 0) {
            DOMUtils.showAlert('يرجى ملء جميع الحقول المطلوبة', 'warning');
            return false;
        }

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.parentEmail)) {
            DOMUtils.showAlert('يرجى إدخال بريد إلكتروني صحيح', 'warning');
            return false;
        }

        return true;
    }

    setupEventListeners() {
        this.parentsSelect.addEventListener('change', async () => {
            const parentId = this.parentsSelect.value;
            if (parentId && parentId !== '0') {
                await this.loadParentDetails(parentId);
            }
        });

        this.updateParentButton.addEventListener('click', async () => {
            const parentData = this.getParentFormData();
            
            if (this.validateParentData(parentData)) {
                if (DOMUtils.confirmAction('هل أنت متأكد من تحديث بيانات ولي الأمر؟')) {
                    await this.updateParent(parentData);
                }
            }
        });
    }
}

// ============================================
// MAIN APPLICATION
// ============================================

class AdminPanel {
    constructor() {
        this.modules = {
            ui: new UIManager(),
            announcements: new AnnouncementsManager(),
            accounts: new AccountsManager(),
            attendance: new AttendanceManager(),
            dynamicForms: new DynamicFormManager(),
            messages: new MessagesManager(),
            marks: new MarksManager(),
            classes: new ClassesManager(),
            students: new StudentsManager(),
            parents: new ParentsManager()
        };
    }

    async initialize() {
        try {
            // Initialize UI first
            this.modules.ui.initialize();
            
            // Initialize all modules
            await Promise.allSettled([
                this.modules.announcements.initialize(),
                this.modules.accounts.initialize(),
                this.modules.attendance.initialize(),
                this.modules.dynamicForms.initialize(),
                this.modules.messages.initialize(),
                this.modules.marks.initialize(),
                this.modules.classes.initialize(),
                this.modules.students.initialize(),
                this.modules.parents.initialize()
            ]);

            console.log('Admin panel initialized successfully');
        } catch (error) {
            console.error('Failed to initialize admin panel:', error);
        }
    }
}

// ============================================
// APPLICATION STARTUP
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    const app = new AdminPanel();
    app.initialize();
});