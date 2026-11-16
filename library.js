// ==========================================
// ЛАБОРАТОРНА РОБОТА №5: Управління Бібліотекою
// ==========================================

// 1. Клас Book (Книга)
class Book {
    #title;
    #author;
    #isAvailable;

    constructor(title, author) {
        this.#title = title;
        this.#author = author;
        this.#isAvailable = true;
    }

    get title() { return this.#title; }
    get author() { return this.#author; }
    get isAvailable() { return this.#isAvailable; }

    borrow() {
        if (!this.#isAvailable) {
            throw new Error(`Книга "${this.#title}" вже зайнята іншим читачем.`);
        }
        this.#isAvailable = false;
    }

    returnBook() {
        this.#isAvailable = true;
    }
}

// 2. Базовий клас User (Користувач)
class User {
    #id;
    #name;
    #email;

    constructor(id, name, email) {
        this.#id = id;
        this.#name = name;
        this.#email = email;
    }

    get id() { return this.#id; }
    get name() { return this.#name; }
    get email() { return this.#email; }
}

// 3. Клас Librarian (Бібліотекар), успадковує User
class Librarian extends User {
    constructor(id, name, email) {
        super(id, name, email);
    }

    addBook(library, title, author) {
        const book = new Book(title, author);
        library.addBookToLibrary(book);
        console.log(`[Бібліотекар ${this.name}] Додано нову книгу: "${title}" - ${author}`);
    }

    removeBook(library, bookTitle) {
        library.removeBookFromLibrary(bookTitle);
        console.log(`[Бібліотекар ${this.name}] Видалено книгу: "${bookTitle}"`);
    }
}

// 4. Клас Reader (Читач), успадковує User
class Reader extends User {
    constructor(id, name, email) {
        super(id, name, email);
    }

    borrowBook(library, bookTitle, dueDate) {
        try {
            library.createLoan(this, bookTitle, dueDate);
        } catch (error) {
            console.error(`[Помилка для читача ${this.name}]: ${error.message}`);
        }
    }

    returnBook(library, bookTitle) {
        try {
            library.closeLoan(this, bookTitle);
        } catch (error) {
            console.error(`[Помилка повернення]: ${error.message}`);
        }
    }
}

// 5. Клас Loan (Позика)
class Loan {
    #book;
    #reader;
    #dueDate;

    constructor(book, reader, dueDate) {
        this.#book = book;
        this.#reader = reader;
        this.#dueDate = new Date(dueDate);
    }

    get book() { return this.#book; }
    get reader() { return this.#reader; }
    get dueDate() { return this.#dueDate; }

    isOverdue() {
        return new Date() > this.#dueDate;
    }
}

// 6. Клас Library (Менеджер системи бібліотеки)
class Library {
    #books = [];
    #users = [];
    #loans = [];

    addBookToLibrary(book) {
        this.#books.push(book);
    }

    removeBookFromLibrary(bookTitle) {
        const index = this.#books.findIndex(b => b.title === bookTitle);
        if (index !== -1) {
            this.#books.splice(index, 1);
        } else {
            throw new Error(`Книгу "${bookTitle}" не знайдено в бібліотеці.`);
        }
    }

    registerUser(user) {
        this.#users.push(user);
    }

    createLoan(reader, bookTitle, dueDate) {
        const book = this.#books.find(b => b.title === bookTitle);
        if (!book) {
            throw new Error(`Книги "${bookTitle}" немає в каталозі бібліотеки.`);
        }
        if (!book.isAvailable) {
            throw new Error(`Книга "${bookTitle}" зараз недоступна (вже позичена).`);
        }

        book.borrow();
        const loan = new Loan(book, reader, dueDate);
        this.#loans.push(loan);
        console.log(`[Успіх] Читач ${reader.name} взяв книгу "${bookTitle}". Термін повернення до: ${dueDate}`);
    }

    closeLoan(reader, bookTitle) {
        const loanIndex = this.#loans.findIndex(l => l.book.title === bookTitle && l.reader.id === reader.id);
        if (loanIndex === -1) {
            throw new Error(`Активну позику для книги "${bookTitle}" у читача ${reader.name} не знайдено.`);
        }

        const loan = this.#loans[loanIndex];
        loan.book.returnBook();
        this.#loans.splice(loanIndex, 1);
        console.log(`[Успіх] Читач ${reader.name} успішно повернув книгу "${bookTitle}" до бібліотеки.`);
    }

    listAvailableBooks() {
        console.log("--- Список доступних книг у бібліотеці ---");
        const available = this.#books.filter(b => b.isAvailable);
        if (available.length === 0) {
            console.log("Наразі немає вільних книг.");
        } else {
            available.forEach(b => console.log(`  • "${b.title}" — автор: ${b.author}`));
        }
    }
}

// ==========================================
// ТЕСТОВІ СЦЕНАРІЇ (Перевірка роботи ООП)
// ==========================================
console.log("=== ІНІЦІАЛІЗАЦІЯ СИСТЕМИ БІБЛІОТЕКИ ===");

const myLibrary = new Library();

// Створюємо користувачів
const librarian = new Librarian(1, "Олена Вікторівна", "librarian@biblio.ua");
const reader1 = new Reader(2, "Олександр Брожик", "alex@gmail.com");
const reader2 = new Reader(3, "Марія", "maria@gmail.com");

myLibrary.registerUser(librarian);
myLibrary.registerUser(reader1);
myLibrary.registerUser(reader2);

// 1. Бібліотекар додає книги
librarian.addBook(myLibrary, "Чистий код", "Роберт Мартін");
librarian.addBook(myLibrary, "Програмування на JavaScript", "Дуглас Крокфорд");

myLibrary.listAvailableBooks();

// 2. Читач бере книгу
console.log("\n--- Тест 1: Взяття книги ---");
reader1.borrowBook(myLibrary, "Чистий код", "2026-12-01");

// 3. Інший читач намагається взяти ту саму книгу (має спрацювати перевірка помилки)
console.log("\n--- Тест 2: Спроба взяти зайняту книгу ---");
reader2.borrowBook(myLibrary, "Чистий код", "2026-12-05");

// 4. Перший читач повертає книгу, і другий бере її
console.log("\n--- Тест 3: Повернення та повторна позика ---");
reader1.returnBook(myLibrary, "Чистий код");
reader2.borrowBook(myLibrary, "Чистий код", "2026-12-10");

myLibrary.listAvailableBooks();
