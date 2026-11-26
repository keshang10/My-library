<!-- Rendered with Twig -->
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Leaf &amp; Ledger | Library management</title>
        <link rel="stylesheet" href="assets/style.css">
        <script src="assets/app.js" defer></script>
    </head>
    <body>
        <div class="shell">
            <aside class="sidebar">
                <a class="brand" href="index.php">
                    <span class="brand-mark" aria-hidden="true">L</span>
                    <span>
                        Leaf &amp;Ledger<small>LIBRARY MANAGEMENT</small>
                    </span>
                </a>
                <div class="nav-label">WORKSPACE</div>
                <nav aria-label="Main navigation">
                    <a class="nav active" href="index.php">▤ &nbsp; Book catalogue</a>
                    <a class="nav " href="index.php?page=add">＋ &nbsp; Add book</a>
                    <a class="nav " href="index.php?page=contact">◇ &nbsp; About &amp; help</a>
                </nav>
                <div class="account">
                    <div class="nav-label">ACCOUNT</div>
                    <p>
                        Signed in as <strong>librarian</strong>
                    </p>
                    <p>Librarian</p>
                    <form action="index.php?page=logout" method="post">
                        <input type="hidden" name="csrf" value="ece9ac0a5fd6e98da7f3d39ff70be030300a068d6a32a3f0ceb362838e58cc5d">
                        <button class="side-button">Sign out</button>
                    </form>
                </div>
                <div class="side-foot">5CS045 · Library project</div>
            </aside>
            <main>
                <header class="topbar">
                    <span>Library workspace / Catalogue</span>
                    <span class="top-badge">LIBRARY CATALOGUE</span>
                </header>
                <section class="hero">
                    <div>
                        <p class="eyebrow">YOUR CAMPUS · YOUR COLLECTION</p>
                        <h1>
                            Discover more.<br>
                            <em> Read beyond.</em>
                        </h1>
                        <p>Explore the catalogue or sign in to maintain the library inventory.</p>
                        <a class="button light" href="index.php?page=add">＋ Add a book</a>
                    </div>
                    <div class="hero-art" aria-hidden="true">
                        <div class="book b1"></div>
                        <div class="book b2"></div>
                        <div class="book b3"></div>
                        <div class="book b4"></div>
                        <div class="book b5"></div>
                        <div class="shelf"></div>
                    </div>
                </section>
                <div class="stats">
                    <div>
                        <span>Total titles</span>
                        <strong>8</strong>
                        <small>Books in the catalogue</small>
                    </div>
                    <div>
                        <span>Genres</span>
                        <strong>5</strong>
                        <small>Different categories</small>
                    </div>
                    <div>
                        <span>Copies</span>
                        <strong>22</strong>
                        <small>Physical inventory</small>
                    </div>
                </div>
                <section class="panel" id="catalogue">
                    <div class="panel-head">
                        <div>
                            <p class="eyebrow">COLLECTION</p>
                            <h2>Book catalogue</h2>
                            <p>Filter by title, author, genre and year together.</p>
                        </div>
                        <a class="button" href="index.php?page=add">＋ Add book</a>
                    </div>
                    <form class="filters" id="search-form" action="index.php" method="get">
                        <label>
                            Title<input type="search" name="title" value="" maxlength="150" placeholder="Search titles">
                        </label>
                        <label>
                            Author<input type="search" name="author" value="" maxlength="120" placeholder="Author name">
                        </label>
                        <label>
                            Genre
                            <select name="genre">
                                <option value="">All genres</option>
                                <option value="Fiction">Fiction</option>
                                <option value="Non-fiction">Non-fiction</option>
                                <option value="Science Fiction">Science Fiction</option>
                                <option value="Fantasy">Fantasy</option>
                                <option value="Mystery">Mystery</option>
                                <option value="Romance">Romance</option>
                                <option value="History">History</option>
                                <option value="Biography">Biography</option>
                                <option value="Technology">Technology</option>
                                <option value="Other">Other</option>
                            </select>
                        </label>
                        <label>
                            Year<input type="number" name="year" min="1" max="2027" value="" placeholder="2023">
                        </label>
                        <div class="filter-actions">
                            <button class="button" type="submit">Search</button>
                            <a class="secondary" href="index.php">Reset</a>
                        </div>
                    </form>
                    <div class="result-head">
                        <span id="result-count" role="status" aria-live="polite">8 titles found</span>
                        <span class="muted">Live search enabled</span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title / Author</th>
                                    <th>Genre</th>
                                    <th>Year</th>
                                    <th>ISBN</th>
                                    <th>Copies</th>
                                    <th>Availability</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="book-rows" data-auth="1">
                                <tr>
                                    <td>
                                        <strong>Pride and Prejudice</strong>
                                        <small>Jane Austen</small>
                                    </td>
                                    <td>Fiction</td>
                                    <td>1813</td>
                                    <td></td>
                                    <td>4</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=1">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=1">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Some Desperate Glory</strong>
                                        <small>Emily Tesh</small>
                                    </td>
                                    <td>Science Fiction</td>
                                    <td>2023</td>
                                    <td></td>
                                    <td>3</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=7">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=7">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>The Creative Act</strong>
                                        <small>Rick Rubin</small>
                                    </td>
                                    <td>Non-fiction</td>
                                    <td>2023</td>
                                    <td></td>
                                    <td>1</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=5">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=5">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>The girl on the train</strong>
                                        <small>Paula Hawkins</small>
                                    </td>
                                    <td>Mystery</td>
                                    <td>2016</td>
                                    <td></td>
                                    <td>4</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=19">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=19">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>The Hobbit</strong>
                                        <small>J. R. R. Tolkien</small>
                                    </td>
                                    <td>Fantasy</td>
                                    <td>1937</td>
                                    <td></td>
                                    <td>3</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=2">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=2">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>The Hound of the Baskervilles</strong>
                                        <small>Arthur Conan Doyle</small>
                                    </td>
                                    <td>Mystery</td>
                                    <td>1902</td>
                                    <td></td>
                                    <td>3</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=9">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=9">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>The Left Hand of Darkness</strong>
                                        <small>Ursula K. Le Guin</small>
                                    </td>
                                    <td>Science Fiction</td>
                                    <td>1969</td>
                                    <td></td>
                                    <td>2</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=4">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=4">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>The Mountain in the Sea</strong>
                                        <small>Ray Nayler</small>
                                    </td>
                                    <td>Science Fiction</td>
                                    <td>2022</td>
                                    <td></td>
                                    <td>2</td>
                                    <td>
                                        <span class="pill">In stock</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?page=edit&amp;id=6">Edit</a>
                                            <a class="danger-link" href="index.php?page=delete&amp;id=6">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p id="empty-state" class="empty" hidden>No books match these filters.</p>
                </section>
                <footer>
                    Leaf &amp;Ledger <span>·</span> Library Management System <span>·</span> PHP &amp;MySQL
                </footer>
            </main>
        </div>
    </body>
</html>
