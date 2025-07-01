<?php
require_once 'db.php';

// ตรวจสอบว่ามี ID ส่งมาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$mango_id = $_GET['id'];

try {
    // เตรียมคำสั่ง SQL สำหรับดึงข้อมูลมะม่วง
    $stmt = $pdo->prepare("SELECT * FROM mangoes WHERE id = ?");
    $stmt->execute([$mango_id]);
    $mango = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าพบข้อมูลหรือไม่
    if (!$mango) {
        header("Location: dashboard.php");
        exit;
    }

    // ฟังก์ชันแปลงข้อมูล checkbox ที่เก็บเป็น CSV ให้เป็น array
    function csv_to_array($csv) {
        if (empty($csv)) return [];
        return explode(',', $csv);
    }

    // แปลงข้อมูลการขยายพันธุ์และการแปรรูป
    $propagation = csv_to_array($mango['propagation_method']);
    $processing = csv_to_array($mango['processing_methods']);

} catch (Exception $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดมะม่วง: <?= htmlspecialchars($mango['name_local']) ?></title>
    
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
        
        .detail-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .section-title {
            font-size: 1.3rem;
            color: var(--primary-dark);
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            background: var(--primary-light);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
        }
        
        .morphology-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .morphology-item {
            text-align: center;
        }
        
        .morphology-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        
        .morphology-img:hover {
            transform: scale(1.05);
        }
        
        .morphology-label {
            margin-top: 10px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }
        
        .info-value {
            color: var(--text-dark);
        }
        
        .badge-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .info-badge {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .btn-action {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }
        
        .btn-action:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            color: white;
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
        }
        
        .btn-back:hover {
            color: var(--primary-dark);
            background: var(--primary-light);
            text-decoration: none;
        }
        
        .header-image {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
        }
        
        .no-image {
            background: var(--light-bg);
            height: 350px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 1.2rem;
        }
        
        .no-image i {
            font-size: 3rem;
            margin-bottom: 15px;
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
            
            .header-image, .no-image {
                height: 250px;
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
            <h1 class="mb-3"><?= htmlspecialchars($mango['name_local']) ?></h1>
            <p class="mb-0 lead"><?= htmlspecialchars($mango['name_sci']) ?></p>
        </div>
        
        <div class="d-flex justify-content-start mb-4">
            <a href="dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> กลับสู่แดชบอร์ด
            </a>
        </div>
        
        <!-- รูปภาพหลัก -->
        <div class="mb-5">
            <?php if (!empty($mango['header_img'])): ?>
                <img src="<?= htmlspecialchars($mango['header_img']) ?>" class="header-image" alt="รูปภาพหลักของมะม่วง <?= htmlspecialchars($mango['name_local']) ?>">
            <?php else: ?>
                <div class="no-image">
                    <div class="text-center">
                        <i class="fas fa-image"></i>
                        <p>ไม่มีรูปภาพหลัก</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- ส่วนข้อมูลหลัก -->
        <div class="row">
            <!-- คอลัมน์ซ้าย: ข้อมูลทั่วไป -->
            <div class="col-lg-6 mb-4">
                <div class="detail-card">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        ข้อมูลทั่วไป
                    </h3>
                    
                    <div class="info-item">
                        <div class="info-label">ชื่อวิทยาศาสตร์</div>
                        <div class="info-value"><?= htmlspecialchars($mango['name_sci']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">ชื่อท้องถิ่น</div>
                        <div class="info-value"><?= htmlspecialchars($mango['name_local']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">ลักษณะของดินที่เหมาะสม</div>
                        <div class="info-value"><?= nl2br(htmlspecialchars($mango['soil'])) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">ระยะเวลาเพาะปลูก</div>
                        <div class="info-value"><?= nl2br(htmlspecialchars($mango['planting_period'])) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">หมวดหมู่</div>
                        <div class="info-value"><?= htmlspecialchars($mango['category']) ?></div>
                    </div>
                </div>
                
                <!-- การขยายพันธุ์ -->
                <div class="detail-card">
                    <h3 class="section-title">
                        <i class="fas fa-seedling"></i>
                        การขยายพันธุ์
                    </h3>
                    
                    <div class="badge-list">
                        <?php if (!empty($propagation)): ?>
                            <?php foreach ($propagation as $method): ?>
                                <span class="info-badge"><?= htmlspecialchars(trim($method)) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="info-value">ไม่มีข้อมูลการขยายพันธุ์</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- การประมวลผล -->
                <div class="detail-card">
                    <h3 class="section-title">
                        <i class="fas fa-cogs"></i>
                        การประมวลผล
                    </h3>
                    
                    <div class="badge-list">
                        <?php if (!empty($processing)): ?>
                            <?php foreach ($processing as $method): ?>
                                <span class="info-badge"><?= htmlspecialchars(trim($method)) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="info-value">ไม่มีข้อมูลการประมวลผล</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- คอลัมน์ขวา: ลักษณะสัณฐานวิทยา -->
            <div class="col-lg-6">
                <div class="detail-card">
                    <h3 class="section-title">
                        <i class="fas fa-leaf"></i>
                        ลักษณะสัณฐานวิทยา
                    </h3>
                    
                    <div class="morphology-grid">
                        <!-- ผล -->
                        <div class="morphology-item">
                            <?php if (!empty($mango['morph_fruit'])): ?>
                                <img src="<?= htmlspecialchars($mango['morph_fruit']) ?>" class="morphology-img" alt="รูปภาพผลมะม่วง">
                            <?php else: ?>
                                <div class="no-image" style="height: 180px;">
                                    <i class="fas fa-apple-alt"></i>
                                </div>
                            <?php endif; ?>
                            <div class="morphology-label">ผล</div>
                        </div>
                        
                        <!-- ต้น -->
                        <div class="morphology-item">
                            <?php if (!empty($mango['morph_tree'])): ?>
                                <img src="<?= htmlspecialchars($mango['morph_tree']) ?>" class="morphology-img" alt="รูปภาพต้นมะม่วง">
                            <?php else: ?>
                                <div class="no-image" style="height: 180px;">
                                    <i class="fas fa-tree"></i>
                                </div>
                            <?php endif; ?>
                            <div class="morphology-label">ต้น</div>
                        </div>
                        
                        <!-- ใบ -->
                        <div class="morphology-item">
                            <?php if (!empty($mango['morph_leaf'])): ?>
                                <img src="<?= htmlspecialchars($mango['morph_leaf']) ?>" class="morphology-img" alt="รูปภาพใบมะม่วง">
                            <?php else: ?>
                                <div class="no-image" style="height: 180px;">
                                    <i class="fas fa-leaf"></i>
                                </div>
                            <?php endif; ?>
                            <div class="morphology-label">ใบ</div>
                        </div>
                        
                        <!-- ดอก -->
                        <div class="morphology-item">
                            <?php if (!empty($mango['morph_flower'])): ?>
                                <img src="<?= htmlspecialchars($mango['morph_flower']) ?>" class="morphology-img" alt="รูปภาพดอกมะม่วง">
                            <?php else: ?>
                                <div class="no-image" style="height: 180px;">
                                    <i class="fas fa-spa"></i>
                                </div>
                            <?php endif; ?>
                            <div class="morphology-label">ดอก</div>
                        </div>
                        
                        <!-- กิ่ง -->
                        <div class="morphology-item">
                            <?php if (!empty($mango['morph_branch'])): ?>
                                <img src="<?= htmlspecialchars($mango['morph_branch']) ?>" class="morphology-img" alt="รูปภาพกิ่งมะม่วง">
                            <?php else: ?>
                                <div class="no-image" style="height: 180px;">
                                    <i class="fas fa-tree"></i>
                                </div>
                            <?php endif; ?>
                            <div class="morphology-label">กิ่ง</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ปุ่มดำเนินการ -->
        <div class="text-center my-5">
            <a href="edit_mango.php?id=<?= $mango_id ?>" class="btn-action">
                <i class="fas fa-edit"></i> แก้ไขข้อมูลมะม่วง
            </a>
            <a href="dashboard.php" class="btn-action" style="background: #6c757d;">
                <i class="fas fa-arrow-left"></i> กลับสู่แดชบอร์ด
            </a>
        </div>
    </div>

    <!-- Bootstrap & jQuery Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // เพิ่มเอฟเฟกต์เมื่อโหลดหน้า
        $(document).ready(function() {
            $('.detail-card').each(function(i) {
                $(this).delay(100 * i).animate({opacity: 1}, 500);
            });
        });
    </script>
</body>
</html>