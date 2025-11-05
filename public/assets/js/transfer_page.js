
document.addEventListener('DOMContentLoaded', () => text_selector()
)

text_selector = () => document.querySelectorAll('.select-btn')
    .forEach(item => item.addEventListener("click", set_txt_select));

function set_txt_select(e) {
    const target = e.currentTarget; // the element that was clicked
    const inputGroup = target.closest('.input-group');  // find nearest .input-group
    const input = inputGroup.querySelector('.select-textbox');  // get the input inside it

    if (input && target.innerText != "Select") {
        input.value = target.innerText.trim();  // set the value
    }

}