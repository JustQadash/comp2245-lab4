async function loadSubjects() {
    try {
        const response = await fetch('subjects.php');

        if (!response.ok) {
            throw new Error('Could not load subjects');
        }

        const subjects = await response.json();
        const subjectSelect = document.querySelector('#subject');

        subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.name;
            subjectSelect.appendChild(option);
        });

    } catch (error) {
        console.error(error);
    }
}


async function loadBooks() {
    const form = document.querySelector('form');
    const tbody = document.querySelector('#results tbody');
    const status = document.querySelector('#status');
    const searchButton = form.querySelector('button');

    const params = new URLSearchParams(new FormData(form));

    searchButton.disabled = true;
    status.textContent = 'Searching...';

    try {
        const response = await fetch(`books.php?${params}`);

        if (!response.ok) {
            throw new Error('Could not load books');
        }

        const books = await response.json();

        tbody.replaceChildren();

        books.forEach(book => {
            const row = document.createElement('tr');

            const title = document.createElement('td');
            const author = document.createElement('td');
            const year = document.createElement('td');
            const subject = document.createElement('td');
            const copies = document.createElement('td');

            title.textContent = book.title;
            author.textContent = book.author;
            year.textContent = book.year;
            subject.textContent = book.subject;
            copies.textContent = `${book.available} of ${book.copies}`;

            if (book.available === 0) {
                copies.classList.add('out');
            }

            row.append(title, author, year, subject, copies);
            tbody.appendChild(row);
        });

        if (books.length === 0) {
            status.textContent = 'No books match your search.';
        } else if (books.length === 1) {
            status.textContent = '1 book found.';
        } else {
            status.textContent = `${books.length} books found.`;
        }

    } catch (error) {
        console.error(error);
        tbody.replaceChildren();
        status.textContent = "Couldn't load the books. Please try again.";

    } finally {
        searchButton.disabled = false;
    }
}


loadSubjects();
loadBooks();

const form = document.querySelector('form');

form.addEventListener('submit', event => {
    event.preventDefault();
    loadBooks();
});

document.querySelector('#subject').addEventListener('change', loadBooks);
document.querySelector('#available').addEventListener('change', loadBooks);