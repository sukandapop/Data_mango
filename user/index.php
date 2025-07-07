<?php
require_once '../admin/db.php';

// ดึงข้อมูลจากตาราง mangoes สำหรับรายการทั้งหมด
$query = "SELECT * FROM mangoes";
$result = $conn->query($query);

if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สายพันธุ์มะม่วง</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
            --accent-color: #ff9800;
            --light-bg: #f8f9fa;
            --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Kanit', sans-serif;
        }

        .hero {
            height: 70vh;
            background-image: url('./image/1-9.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-color: rgba(46, 125, 50, 0.5);
            background-blend-mode: darken;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            margin-top: 2rem;
        }

        .hero-contact {
            margin-top: 5rem;
        }

        .hero h1 {
            color: #fff;
            font-size: 60px;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(46, 125, 50, 0.3);
        }

        .hero p,
        .hero p samp {
            color: #fff;
            font-size: 20px;
            margin: 1rem 0;
            font-family: "Kanit", sans-serif;
        }

        .button-2 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 1.5rem;
        }

        .button-2 a {
            border-radius: 24px;
            padding: 0.6rem 2rem;
            font-weight: bold;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            background-color: #fff;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            box-shadow: var(--card-shadow);
        }

        .button-2 a.cta-button {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border: none;
        }

        .button-2 a.cta-button.bg-white {
            background: #fff;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .button-2 a:hover {
            background: var(--accent-color);
            color: #fff;
            border-color: var(--accent-color);
            box-shadow: 0 8px 24px rgba(255, 152, 0, 0.13);
        }

        .mango-card {
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            background-color: #fff;
            overflow: hidden;
            border: 2px solid var(--secondary-color);
        }

        .mango-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(46, 125, 50, 0.18);
            border-color: var(--accent-color);
        }

        .mango-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            padding: 10px;
            background: var(--light-bg);
            border-bottom: 1px solid #eee;
        }

        .mango-card .card-body {
            text-align: center;
            padding: 1.2rem 0.8rem;
        }

        .mango-card .card-title {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .mango-card .text-muted {
            color: var(--secondary-color) !important;
        }

        .fw-bold {
            color: var(--accent-color);
        }

        .container h2 {
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 1px;
        }

        .input-group-text,
        .form-control,
        .form-select {
            border-radius: 20px !important;
        }

        .input-group-text {
            background: var(--light-bg);
            border: 1px solid var(--secondary-color);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, .15);
        }
    </style>
</head>

<body>
    <div class="container py-1">
        <div class="hero text-center">
            <div class="hero-contact">
                <h1>สวนมะม่วงลุงเผือก<br />จังหวัดเลย</h1>
                <p>เป็นฐานข้อมูลรวบรวมข้อมูลเกี่ยวกับมะม่วงในจังหวัดเลย ครอบคลุมข้อมูลด้านต่างๆ
                    <br>
                    <samp>กรณีศึกษา สวนลุงเผือก บ.บุฮม อ.เชียงคาน จ.เลย</samp>
                </p>
            
            </div>
        </div>


        <h2 class="text-center mb-4 mt-5">สายพันธุ์มะม่วง</h2>
        <br>
        <div class="mb-4 d-flex justify-content-center">
            <div class="input-group" style="max-width: 500px;"> 
                <span class="input-group-text bg-white border-end-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#888" class="bi bi-search"
                        viewBox="0 0 16 16">
                        <path d="M11 6a5 5 0 1 1-1.001-9.999A5 5 0 0 1 11 6zm-1 0a4 4 0 1 0-8 0 4 4 0 0 0 8 0zm6.707 11.293-3.387-3.387A6.978 6.978 0 0 0 13 6a7 7 0 1 0-7 7 6.978 6.978 0 0 0 3.906-1.08l3.387 3.387a1 1 0 0 0 1.414-1.414z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="ค้นหาสายพันธุ์มะม่วง"
                    aria-label="ค้นหาสายพันธุ์มะม่วง">
                <select id="categorySelect" class="form-select ms-2" style="max-width:180px;">
                    <option value="">ทุกประเภท</option>
                    <option value="เชิงพาณิชย์">เชิงพาณิชย์</option>
                    <option value="เชิงอนุรักษ์">เชิงอนุรักษ์</option>
                    <option value="บริโภคในครัวเรือน">บริโภคในครัวเรือน</option>
                </select>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="mangoList">
            <?php
            // แสดงรายการมะม่วงทั้งหมด
            $result->data_seek(0); // รีเซ็ต pointer เผื่อใช้ $result ซ้ำ
            while ($row = $result->fetch_assoc()) {
                $img_file = isset($row['morph_fruit']) ? basename($row['morph_fruit']) : null;
                $fruit_image = $img_file ? "../uploads/{$img_file}" : null;
                $name = isset($row['name_local']) ? $row['name_local'] : "ไม่ทราบชื่อ";
                $category = isset($row['category']) ? $row['category'] : "ไม่ทราบประเภท";
                $morph_fruit = isset($row['morph_fruit']) ? $row['morph_fruit'] : "ไม่มีข้อมูลลักษณะผล";

                $abs_path = __DIR__ . "/../uploads/" . $img_file;

                echo "<div class='col mango-item' data-category='" . htmlspecialchars($category, ENT_QUOTES) . "'>
                    <a href='mango_detail.php?name=" . urlencode($name) . "' class='text-decoration-none text-dark'>
                        <div class='card mango-card'>";
                echo "      <div class='card-img-top text-center'>";
              
                if ($img_file && file_exists($abs_path)) {
                    echo "<img src='{$fruit_image}' class='card-img-top' alt='{$name}'>";
                } else {
                    echo "<div class='text-center py-5'>ไม่มีรูปภาพ</div>";
                }
                echo "      </div>
                            <div class='card-body'>
                                <h5 class='card-title'>{$name}</h5>
                            
                            </div>
                        </div>
                    </a>
                  </div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('input', filterMangoes);
        document.getElementById('categorySelect').addEventListener('change', filterMangoes);

        function filterMangoes() {
            let filter = document.getElementById('searchInput').value.toLowerCase();
            let category = document.getElementById('categorySelect').value;
            let mangoItems = document.querySelectorAll('.mango-item');

            mangoItems.forEach(function(item) {
                let name = item.querySelector('.card-title').textContent.toLowerCase();
                let cat = item.getAttribute('data-category');
                let nameMatch = name.includes(filter);
                let catMatch = !category || cat === category;
                item.style.display = (nameMatch && catMatch) ? "block" : "none";
            });
        }
    </script>
</body>

</html>