<?php
include 'function.php';

$blok_id = $_GET['blok_id'] ?? '';
$query = "SELECT blok.*, status.status_name FROM blok JOIN status ON blok.status_id = status.status_id WHERE blok_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $blok_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$statusQuery = "SELECT * FROM status";
$statusResult = $conn->query($statusQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blok_name = $_POST['blok_name'];
    $tahun = $_POST['tahun_tanam'];  // New field
    $bulan = $_POST['bulan_tanam'];  // New field
    $luas_tanah = $_POST['luas_tanah'];
    $jumlah_pohon = $_POST['jumlah_pohon'];
    $status_id = $_POST['status_id'];

    $updateResult = updateBlokData($blok_id, $blok_name, $tahun, $bulan, $luas_tanah, $jumlah_pohon, $status_id);

    if ($updateResult === true) {
        echo "<script>alert('Update successful'); window.location='data-Blok.php';</script>";
    } else {
        echo "Update failed: " . $updateResult;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RFID</title>

    <link rel="shortcut icon" href="./assets/compiled/svg/favicon.svg" type="image/x-icon" />
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png" />

    <link rel="stylesheet" href="./assets/compiled/css/app.css" />
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css" />
    <link rel="stylesheet" href="./assets/compiled/css/iconly.css" />
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="Laporan.php"><img src="assets/Image/logo.png" alt="Logo" style="width: 100px; height: 100px;" srcset="" /></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer" />
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor" d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-item has-sub active">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Laporan</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="Laporan.php" class="submenu-link">List Data Pohon</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="data-Status.php" class="submenu-link">List Data Master Status</a>
                                </li>
                                <li class="submenu-item active">
                                    <a href="data-Blok.php" class="submenu-link">List Data Master Blok</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a href="timbangan.php" class="sidebar-link">
                                <i class="bi bi-file-earmark-medical-fill"></i>
                                <span>Input Data Timbangan</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="pohon.php" class="sidebar-link">
                                <i class="bi bi-file-earmark-medical-fill"></i>
                                <span>Input Data Pohon</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>EDIT DATA</h3>
            </div>
            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <header class="mb-3">
                                <a href="data-Blok.php" class="btn btn-primary">Back to List</a>
                            </header>
                            <form class="form form-horizontal" id="editForm" action="edit-Blok.php?blok_id=<?php echo htmlspecialchars($data['blok_id']); ?>" method="post">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="id">ID</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input type="text" id="blok_id" class="form-control" name="blok_id" value="<?php echo htmlspecialchars($data['blok_id']); ?>" disabled />
                                        </div>
                                        <div class="col-md-2">
                                            <label for="blok_name">Nama Blok</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input type="text" id="blok_name" class="form-control" name="blok_name" value="<?php echo htmlspecialchars($data['blok_name']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label for="tanggal_tanam">Tahun Tanam</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input type="text" id="tahun_tanam" class="form-control" name="tahun_tanam" value="<?php echo htmlspecialchars($data['tahun']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label for="tanggal_tanam">Bulan Tanam</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input type="text" id="bulan_tanam" class="form-control" name="bulan_tanam" value="<?php echo htmlspecialchars($data['bulan']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label for="luas_tanah">Luas Tanah</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input type="text" id="luas_tanah" class="form-control" name="luas_tanah" value="<?php echo htmlspecialchars($data['luas_tanah']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label for="jumlah_pohon">Jumlah Pohon</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input type="text" id="jumlah_pohon" class="form-control" name="jumlah_pohon" value="<?php echo htmlspecialchars($data['jumlah_pohon']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label for="status_name">Keterangan Status</label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <fieldset class="form-group">
                                                <select class="form-select" id="status_id" name="status_id">
                                                    <?php while ($status = $statusResult->fetch_assoc()) : ?>
                                                        <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $data['status_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($status['status_name']) ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-sm-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Update</button>
                                            <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                <?php include 'footer-dashboard.php'; ?>
            </div>
        </div>
        <script src="assets/static/js/components/dark.js"></script>
        <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="assets/compiled/js/app.js"></script>
        <script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
        <script src="assets/static/js/pages/dashboard.js"></script>
</body>

</html>