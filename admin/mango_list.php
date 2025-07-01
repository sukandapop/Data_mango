<?php
// filepath: c:\xampp\htdocs\Data_mango\admin\mango_list.php
require_once 'db.php';

// ดึงข้อมูลมะม่วงทั้งหมด
$sql = "SELECT id, name_local, category, morph_fruit FROM mangoes ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการมะม่วงทั้งหมด</title>
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4CAF50;
            --primary-dark: #388E3C;
            --primary-light: #C8E6C9;
            --accent-color: #FF9800;
            --text-dark: #333;
            --text-light: #666;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --border-radius: 12px;
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Prompt', sans-serif;
            color: var(--text-dark);
            padding: 20px 0 50px;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        }
        
        .mango-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 40px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }
        
        .mango-header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .header-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.2);
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .mango-card {
            min-height: 350px;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }
        
        .mango-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
        }
        
        .card-img-top {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: var(--light-bg);
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
            transition: all 0.4s ease;
        }
        
        .mango-card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        .no-image-placeholder {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa, #e4edf5);
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
        }
        
        .no-image-placeholder i {
            font-size: 3rem;
            color: #c8e6c9;
        }
        
        .card-body {
            padding: 20px;
            position: relative;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 12px;
        }
        
        .card-category {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .btn-view {
            background: var(--primary-color);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }
        
        .btn-view:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .mango-count {
            background: white;
            padding: 12px 20px;
            border-radius: 30px;
            box-shadow: var(--shadow-sm);
            font-weight: 500;
            color: var(--primary-dark);
            display: inline-block;
            border: 1px solid var(--primary-light);
        }
        
        .mango-count i {
            color: var(--primary-color);
            margin-right: 8px;
        }
        
        .btn-back {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            padding: 8px 15px;
            border-radius: 30px;
            background: var(--light-bg);
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            color: var(--primary-dark);
            background: var(--primary-light);
            text-decoration: none;
        }
        
        /* ปรับปรุงส่วนค้นหาใหม่ */
        .search-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 15px 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        
        .search-container {
            display: flex;
            flex-grow: 1;
            max-width: 500px;
            margin-left: auto;
        }
        
        .search-input {
            border: 1px solid #e0e0e0;
            border-radius: 30px 0 0 30px;
            padding: 12px 20px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: none;
            outline: none;
        }
        
        .search-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 30px 30px 0;
            padding: 0 25px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-btn:hover {
            background: var(--primary-dark);
        }
        
        .no-results {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            grid-column: 1 / -1;
        }
        
        .no-results-icon {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .header-icon {
                width: 70px;
                height: 70px;
                font-size: 2.5rem;
            }
            
            .mango-header h1 {
                font-size: 1.8rem;
            }
            
            .search-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-container {
                max-width: 100%;
                margin-left: 0;
            }
            
            .mango-count {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mango-header text-center position-relative">
            <div class="header-icon">
                <i class="fas fa-mango"></i>
            </div>
            <h1 class="mb-3">รายการมะม่วงทั้งหมด</h1>
            <p class="mb-0 lead">ค้นหาและจัดการมะม่วงต่างๆ ในระบบ</p>
        </div>
        
        <div class="d-flex justify-content-start mb-4">
            <a href="dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> กลับสู่แดชบอร์ด
            </a>
        </div>
        
        <!-- ส่วนค้นหาและนับจำนวน - ปรับปรุงใหม่ -->
        <div class="search-section">
            <div class="mango-count">
                <i class="fas fa-tree"></i>
                <?php 
                    $count = ($result && $result->num_rows > 0) ? $result->num_rows : 0;
                    echo "พบมะม่วงทั้งหมด {$count} พันธุ์";
                ?>
            </div>
            
            <div class="search-container">
                <input type="text" class="search-input" placeholder="ค้นหามะม่วง...">
                <button class="search-btn" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <!-- ส่วนแสดงรายการมะม่วง -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="mangoList">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="mango-card h-100">
                            <?php if (!empty($row['morph_fruit'])): ?>
                                <img src="/Data_mango/<?= htmlspecialchars($row['morph_fruit']) ?>" class="card-img-top" alt="ผลมะม่วง <?= htmlspecialchars($row['name_local']) ?>">
                            <?php else: ?>
                                <div class="no-image-placeholder">
                                    <i class="fas fa-mango"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name_local']) ?></h5>
                                <div class="card-category"><?= htmlspecialchars($row['category']) ?></div>
                                <a href="view_mango.php?id=<?= urlencode($row['id']) ?>" class="btn btn-view">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <h4>ไม่พบข้อมูลมะม่วง</h4>
                    <p>ยังไม่มีมะม่วงในระบบ กรุณาเพิ่มมะม่วงพันธุ์ใหม่</p>
                    <a href="add_mango.php" class="btn btn-success mt-3">
                        <i class="fas fa-plus me-2"></i>เพิ่มมะม่วงใหม่
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap & jQuery Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ฟังก์ชันค้นหามะม่วงแบบเรียลไทม์
        $(document).ready(function() {
            $('.search-input').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                const mangoCards = $('#mangoList .col');
                
                mangoCards.each(function() {
                    const cardText = $(this).text().toLowerCase();
                    if (cardText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                
                // แสดงข้อความหากไม่พบผลลัพธ์
                const visibleCards = mangoCards.filter(':visible').length;
                if (visibleCards === 0 && searchTerm !== '') {
                    $('#mangoList').html(`
                        <div class="no-results">
                            <div class="no-results-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4>ไม่พบข้อมูลมะม่วง</h4>
                            <p>ไม่พบมะม่วงที่ตรงกับคำค้นหา "${searchTerm}"</p>
                            <button class="btn btn-primary mt-3" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-2"></i>แสดงทั้งหมด
                            </button>
                        </div>
                    `);
                }
            });
            
            // ปุ่มค้นหา
            $('.search-btn').on('click', function() {
                $('.search-input').trigger('keyup');
            });
        });
    </script>
</body>
</html>