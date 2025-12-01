function countWords(str) {
    if (!str) return 0;
    return str.trim().split(/\s+/).filter(word => word.length > 0).length;
}


$(document).ready(function () {
    $('#blogForm').submit(function (e) {
        let isValid = true

        const data = $(this).serializeArray()
        data.forEach(element => {
            if (element.name == '_token') {
                return
            }
            $(`#${element.name}Error`).text('')
            if (element.name == 'title' && (countWords(element.value) < 5 || countWords(element.value) > 10)) {
                isValid = false
                $(`#${element.name}Error`).text(`Title must contain 5-10 words.`)
            }
            if (element.name == 'excerpt' && (countWords(element.value) < 10 || countWords(element.value) > 20)) {
                isValid = false
                $(`#${element.name}Error`).text(`Excerpt must contain 10-20 words.`)
            }
            if (element.name == 'content' && (countWords(element.value) < 30 || countWords(element.value) > 100)) {
                isValid = false
                $(`#${element.name}Error`).text(`Content must contain 30-100 words.`)
            }
            if (element.value == '') {
                isValid = false
                $(`#${element.name}Error`).text(`${element.name.charAt(0).toUpperCase() + element.name.slice(1)} field cannot be empty.`)
            }
        });

        const image = $('#image')[0]
        const maxFileSize = 2048;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

        if (image.files.length > 0) {
            const file = image.files[0];
            const fileType = file.type;
            const fileSize = file.size / 1024;
            if (!allowedTypes.includes(fileType)) {
                isValid = false;
                $('#imageError').text('Invalid file type. Only JPEG, PNG, and WebP are allowed.')
            }
            else if (fileSize > maxFileSize) {
                isValid = false;
                $('#imageError').text(`File size must be less than ${maxFileSize / 1024} MB.`)
            } else {
                $('#imageError').text('')
            }
        } else {
            isValid = false
            $('#imageError').text('Featured image is required.')

        }
        if (!isValid) {
            e.preventDefault();

        }
    })
})






// logout
