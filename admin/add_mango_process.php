<?php
// ตั้งค่า Error Reporting เพื่อช่วยในการดีบัก (สามารถปิดได้ใน Production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';

    if (!isset($pdo)) {
        die("ข้อผิดพลาด: ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
    }

    // ฟังก์ชันช่วย sanitize ชื่อไฟล์
    function safe_filename($filename)
    {
        // แทนที่อักขระที่ไม่ปลอดภัยด้วยขีดล่าง
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
        // จำกัดความยาวชื่อไฟล์เพื่อป้องกันปัญหา
        return substr($filename, 0, 200);
    }

    // รับค่าจากฟอร์มและป้องกัน XSS
    $name_sci = htmlspecialchars(trim($_POST['name_sci'] ?? ''), ENT_QUOTES, 'UTF-8');
    $name_local = htmlspecialchars(trim($_POST['name_local'] ?? ''), ENT_QUOTES, 'UTF-8');
    $soil = htmlspecialchars(trim($_POST['soil'] ?? ''), ENT_QUOTES, 'UTF-8');
    $planting_period = htmlspecialchars(trim($_POST['planting_period'] ?? ''), ENT_QUOTES, 'UTF-8');

    // แปลง array ของ checkbox เป็น string คั่นด้วย comma
    $category = isset($_POST['category']) ? implode(',', array_map('htmlspecialchars', $_POST['category'])) : '';
    $propagation = isset($_POST['propagation']) ? implode(',', array_map('htmlspecialchars', $_POST['propagation'])) : '';
    $processing = isset($_POST['processing']) ? implode(',', array_map('htmlspecialchars', $_POST['processing'])) : '';

    $upload_dir = __DIR__ . '/../uploads/'; // กำหนดโฟลเดอร์อัปโหลดที่ถูกต้อง
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
    }

    // อัพโหลดรูป header
    $header_img = '';
    if (isset($_FILES['header_img']) && $_FILES['header_img']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['header_img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $new_filename = 'header_' . uniqid() . '.' . $ext;
            $safe_path = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['header_img']['tmp_name'], $safe_path)) {
                $header_img = 'uploads/' . $new_filename; // Path สำหรับบันทึกลง DB
            } else {
                error_log("Failed to move uploaded file for header_img: " . $_FILES['header_img']['tmp_name'] . " to " . $safe_path);
            }
        } else {
            error_log("Invalid file type for header_img: " . $ext);
        }
    }

    // อัพโหลดรูปสัณฐานวิทยา
    $morph_fruit = '';
    $morph_tree = '';
    $morph_leaf = '';
    $morph_flower = '';
    $morph_branch = '';

    $img_fields = [
        'morph_fruit' => &$morph_fruit,
        'morph_tree' => &$morph_tree,
        'morph_leaf' => &$morph_leaf,
        'morph_flower' => &$morph_flower,
        'morph_branch' => &$morph_branch
    ];

    foreach ($img_fields as $field => &$var) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $new_filename = $field . '_' . uniqid() . '.' . $ext;
                $safe_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $safe_path)) {
                    $var = 'uploads/' . $new_filename; // Path สำหรับบันทึกลง DB
                } else {
                    error_log("Failed to move uploaded file for " . $field . ": " . $_FILES[$field]['tmp_name'] . " to " . $safe_path);
                }
            } else {
                error_log("Invalid file type for " . $field . ": " . $ext);
            }
        }
    }
    unset($var); // ยกเลิกการอ้างอิงหลังจากวนลูป

    try {
        $stmt = $pdo->prepare("INSERT INTO mangoes 
                (name_sci, name_local, soil, planting_period, category, propagation, processing, header_img, morph_fruit, morph_tree, morph_leaf, morph_flower, morph_branch)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $name_sci,
            $name_local,
            $soil,
            $planting_period,
            $category,
            $propagation,
            $processing,
            $header_img,
            $morph_fruit,
            $morph_tree,
            $morph_leaf,
            $morph_flower,
            $morph_branch
        ]);

        header("Location: dashboard.php?success=1");
        exit;
    } catch (Exception $e) {
        // คุณสามารถแสดง error หรือ log ได้ที่นี่
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลมะม่วง</title>
    
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
        
        .mango-header::after {
            content: "";
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 150px;
            height: 150px;
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
        
        .form-section {
            background: var(--white);
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-section:hover {
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
        
        .form-control, .form-select {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }
        
        .image-preview {
            width: 100%;
            height: 180px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            overflow: hidden;
            background-color: var(--light-bg);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .image-preview:hover {
            border-color: var(--primary-color);
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 120px;
            width: auto;
            height: auto;
            display: none;
            margin: 0 auto;
        }
        
        .image-preview .preview-icon {
            font-size: 3rem;
            color: #ddd;
        }
        
        .image-preview .preview-text {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 14px 35px;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
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
        
        .required::after {
            content: " *";
            color: #e53935;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-dark);
        }
        
        .form-check {
            margin-bottom: 12px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-label {
            color: var(--text-dark);
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .info-text {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-top: 6px;
        }
        
        .form-container {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .morphology-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .morphology-item {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .morphology-grid {
                grid-template-columns: 1fr;
            }
            
            .header-icon {
                width: 70px;
                height: 70px;
                font-size: 2.5rem;
            }
            
            .mango-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mango-header text-center position-relative">
                    <div class="header-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h1 class="mb-3">เพิ่มข้อมูลมะม่วงพันธุ์ใหม่</h1>
                    <p class="mb-0 lead">กรอกข้อมูลมะม่วงพันธุ์ใหม่ลงในระบบเพื่อการจัดการข้อมูลที่สมบูรณ์</p>
                </div>

                <div class="d-flex justify-content-start mb-4">
                    <a href="Dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> กลับสู่แดชบอร์ด
                    </a>
                </div>

                <div class="form-container">
                    <form id="mangoForm" action="add_mango_process.php" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <!-- คอลัมน์ 1: ข้อมูลทั่วไป -->
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="form-section">
                                    <h3 class="section-title">
                                        <i class="fas fa-info-circle"></i>
                                        ข้อมูลทั่วไป
                                    </h3>
                                    
                                    <div class="form-group">
                                        <label class="form-label required">อัพโหลดรูปภาพหลัก</label>
                                        <input type="file" name="header_img" class="form-control" accept="image/*" required onchange="previewImage(this, 'headerPreview')">
                                        <div class="info-text">รูปภาพขนาดแนะนำ 1200x400 พิกเซล</div>
                                        <div class="image-preview mt-3" id="headerPreview">
                                            <div class="preview-icon">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div class="preview-text">รูปภาพหลักมะม่วง</div>
                                            <img src="" alt="Header Preview">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label required">ชื่อวิทยาศาสตร์</label>
                                        <input type="text" name="name_sci" class="form-control" placeholder="กรุณากรอกชื่อวิทยาศาสตร์" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label required">ชื่อท้องถิ่น</label>
                                        <input type="text" name="name_local" class="form-control" placeholder="กรุณากรอกชื่อท้องถิ่น" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label required">ลักษณะของดินที่เหมาะสม</label>
                                        <textarea name="soil" class="form-control" rows="3" placeholder="อธิบายลักษณะดินที่เหมาะสำหรับปลูกมะม่วงพันธุ์นี้" required></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label required">ระยะเวลาเพาะปลูก</label>
                                        <textarea name="planting_period" class="form-control" rows="3" placeholder="ระบุระยะเวลาเพาะปลูกจนถึงเก็บเกี่ยว" required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- คอลัมน์ 2: การขยายพันธุ์ + หมวดหมู่ + การประมวลผล -->
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="form-section mb-4">
                                    <h3 class="section-title">
                                        <i class="fas fa-seedling"></i>
                                        การขยายพันธุ์
                                    </h3>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="propagation[]" value="การเพาะจากเมล็ด" id="seed">
                                        <label class="form-check-label" for="seed">การเพาะจากเมล็ด</label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="propagation[]" value="เสียบยอด" id="grafting">
                                        <label class="form-check-label" for="grafting">เสียบยอด</label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="propagation[]" value="ทาบกิ่ง" id="layering">
                                        <label class="form-check-label" for="layering">ทาบกิ่ง</label>
                                    </div>
                                </div>
                                
                                <div class="form-section mb-4">
                                    <h3 class="section-title">
                                        <i class="fas fa-tags"></i>
                                        หมวดหมู่มะม่วง
                                    </h3>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" value="เชิงพาณิชย์" id="namdokmai" required>
                                        <label class="form-check-label" for="namdokmai">เชิงพาณิชย์</label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" value="เชิงอนุรักษ์" id="khieo">
                                        <label class="form-check-label" for="khieo">เชิงอนุรักษ์</label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" value="บริโภคครัวเรือน" id="other_category">
                                        <label class="form-check-label" for="other_category">บริโภคครัวเรือน</label>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <h3 class="section-title">
                                        <i class="fas fa-cogs"></i>
                                        การแปรรูป
                                    </h3>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="processing[]" value="การทำแห้ง" id="drying">
                                        <label class="form-check-label" for="drying">การทำแห้ง</label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="processing[]" value="การแปรรูป" id="processing">
                                        <label class="form-check-label" for="processing">การแปรรูป</label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="processing[]" value="การบรรจุหีบห่อ" id="packaging">
                                        <label class="form-check-label" for="packaging">การบรรจุหีบห่อ</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- คอลัมน์ 3: สัณฐานวิทยา -->
                            <div class="col-lg-12">
                                <div class="form-section">
                                    <h3 class="section-title">
                                        <i class="fas fa-leaf"></i>
                                        ลักษณะสัณฐานวิทยา
                                    </h3>
                                    <div class="morphology-grid">
                                        <div class="form-group morphology-item">
                                            <label class="form-label">ผล</label>
                                            <input type="file" name="morph_fruit" class="form-control" accept="image/*" onchange="previewImage(this, 'fruitPreview')">
                                            <div class="image-preview mt-2" id="fruitPreview">
                                                <div class="preview-icon">
                                                    <i class="fas fa-apple-alt"></i>
                                                </div>
                                                <div class="preview-text">รูปภาพผลมะม่วง</div>
                                                <img src="" alt="Fruit Preview">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group morphology-item">
                                            <label class="form-label">ต้น</label>
                                            <input type="file" name="morph_tree" class="form-control" accept="image/*" onchange="previewImage(this, 'treePreview')">
                                            <div class="image-preview mt-2" id="treePreview">
                                                <div class="preview-icon">
                                                    <i class="fas fa-tree"></i>
                                                </div>
                                                <div class="preview-text">รูปภาพต้นมะม่วง</div>
                                                <img src="" alt="Tree Preview">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group morphology-item">
                                            <label class="form-label">ใบ</label>
                                            <input type="file" name="morph_leaf" class="form-control" accept="image/*" onchange="previewImage(this, 'leafPreview')">
                                            <div class="image-preview mt-2" id="leafPreview">
                                                <div class="preview-icon">
                                                    <i class="fas fa-leaf"></i>
                                                </div>
                                                <div class="preview-text">รูปภาพใบมะม่วง</div>
                                                <img src="" alt="Leaf Preview">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group morphology-item">
                                            <label class="form-label">ดอก</label>
                                            <input type="file" name="morph_flower" class="form-control" accept="image/*" onchange="previewImage(this, 'flowerPreview')">
                                            <div class="image-preview mt-2" id="flowerPreview">
                                                <div class="preview-icon">
                                                    <i class="fas fa-spa"></i>
                                                </div>
                                                <div class="preview-text">รูปภาพดอกมะม่วง</div>
                                                <img src="" alt="Flower Preview">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group morphology-item">
                                            <label class="form-label">กิ่ง</label>
                                            <input type="file" name="morph_branch" class="form-control" accept="image/*" onchange="previewImage(this, 'branchPreview')">
                                            <div class="image-preview mt-2" id="branchPreview">
                                                <div class="preview-icon">
                                                    <i class="fas fa-tree"></i>
                                                </div>
                                                <div class="preview-text">รูปภาพกิ่งมะม่วง</div>
                                                <img src="" alt="Branch Preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center my-5">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save me-2"></i> บันทึกข้อมูลมะม่วง
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            <a href="Dashboard.php" class="btn-back">
                                <i class="fas fa-arrow-left me-2"></i> กลับสู่แดชบอร์ด
                            </a>
                        </div>
                    </form>
                </div>
                <!-- เพิ่ม Modal สำหรับแจ้งเตือนบันทึกสำเร็จ ไว้ก่อนปิด </body> -->
                <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel"><i class="fas fa-check-circle me-2"></i>บันทึกข้อมูลสำเร็จ</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mb-0">ข้อมูลมะม่วงถูกบันทึกเรียบร้อยแล้ว</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <a href="Dashboard.php" class="btn btn-success"><i class="fas fa-arrow-left me-2"></i>กลับสู่แดชบอร์ด</a>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Preview Function -->
    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const img = preview.querySelector('img');
            const icon = preview.querySelector('.preview-icon');
            const text = preview.querySelector('.preview-text');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                    if (text) text.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                img.style.display = 'none';
                if (icon) icon.style.display = 'flex';
                if (text) text.style.display = 'block';
            }
        }
        
        // Add animation to form sections on scroll
        $(document).ready(function() {
            $('.form-section').each(function() {
                $(this).css('opacity', '0');
            });
            
            $(window).scroll(function() {
                $('.form-section').each(function() {
                    var position = $(this).offset().top;
                    var scrollPosition = $(window).scrollTop() + $(window).height() * 0.9;
                    
                    if (position < scrollPosition) {
                        $(this).css({
                            'opacity': '1',
                            'transform': 'translateY(0)'
                        });
                    }
                });
            }).scroll(); // Trigger scroll event on page load
        });
    </script>
    <script>
        // ฟังก์ชันแสดง Modal เมื่อมี success=1 ใน URL
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === '1') {
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                // ลบ query string ออกจาก url หลังแสดง modal
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>