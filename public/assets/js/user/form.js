document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const studyProgramGroup = document.getElementById('study-program-group');
    const studyProgramSelect = document.getElementById('study_program');

    roleSelect.addEventListener('change', function () {
        if (this.value === 'kaprodi') {
            studyProgramGroup.style.display = 'block';
            studyProgramSelect.setAttribute('required', 'required');
        } else {
            studyProgramGroup.style.display = 'none';
            studyProgramSelect.removeAttribute('required');
            studyProgramSelect.value = '';
        }
    });
});