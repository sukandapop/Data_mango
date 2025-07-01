<?php
    // filepath: c:\xampp\htdocs\Data_mango\admin\edit_mango.php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'db.php';

    // ตรวจสอบ id
    if (!isset($_GET['id'])) {
        header("Location: mango_list.php");
        exit;
    }
    $id = intval($_GET['id']);

    // ดึงข้อมูลเดิม
    $stmt = $conn->prepare("SELECT * FROM mangoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $mango = $result->fetch_assoc();
    if (!$mango) {
        echo "ไม่พบข้อมูล";
        exit;
    }

    // ฟังก์ชัน sanitize ชื่อไฟล์
    function safe_filename($filename) {
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
        return substr($filename, 0, 200);
    }

    // เมื่อ submit ฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name_sci = htmlspecialchars(trim($_POST['name_sci'] ?? ''), ENT_QUOTES, 'UTF-8');
        $name_local = htmlspecialchars(trim($_POST['name_local'] ?? ''), ENT_QUOTES, 'UTF-8');
        $soil = htmlspecialchars(trim($_POST['soil'] ?? ''), ENT_QUOTES, 'UTF-8');
        $planting_period = htmlspecialchars(trim($_POST['planting_period'] ?? ''), ENT_QUOTES, 'UTF-8');

        // หมวดหมู่ (radio)
        $category = isset($_POST['category']) ? htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8') : null;

        // การขยายพันธุ์ (checkbox)
        if (isset($_POST['propagation'])) {
            if (is_array($_POST['propagation'])) {
                $propagation = implode(',', array_map(function($v) {
                    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                }, $_POST['propagation']));
            } else {
                $propagation = htmlspecialchars($_POST['propagation'], ENT_QUOTES, 'UTF-8');
            }
        } else {
            $propagation = null;
        }

        // การแปรรูป (checkbox)
        if (isset($_POST['processing'])) {
            if (is_array($_POST['processing'])) {
                $processing = implode(',', array_map(function($v) {
                    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                }, $_POST['processing']));
            } else {
                $processing = htmlspecialchars($_POST['processing'], ENT_QUOTES, 'UTF-8');
            }
        } else {
            $processing = null;
        }

        // อัปโหลดรูป (ถ้ามี)
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $fields_img = [
            'header_img', 'morph_fruit', 'morph_tree', 'morph_leaf', 'morph_flower', 'morph_branch'
        ];
        $img_data = [];
        foreach ($fields_img as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowed)) {
                    $new_filename = $field . '_' . uniqid() . '.' . $ext;
                    $safe_path = $upload_dir . $new_filename;
                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $safe_path)) {
                        $img_data[$field] = 'uploads/' . $new_filename;
                    } else {
                        $img_data[$field] = $mango[$field];
                    }
                } else {
                    $img_data[$field] = $mango[$field];
                }
            } else {
                $img_data[$field] = $mango[$field];
            }
        }

        // อัปเดตข้อมูล
        $stmt = $conn->prepare("UPDATE mangoes SET 
            name_sci=?, name_local=?, soil=?, planting_period=?, category=?, propagation=?, processing=?,
            header_img=?, morph_fruit=?, morph_tree=?, morph_leaf=?, morph_flower=?, morph_branch=?
            WHERE id=?");
        $stmt->bind_param(
            "sssssssssssssi", // 13 s + 1 i
            $name_sci,
            $name_local,
            $soil,
            $planting_period,
            $category,
            $propagation,
            $processing,
            $img_data['header_img'],
            $img_data['morph_fruit'],
            $img_data['morph_tree'],
            $img_data['morph_leaf'],
            $img_data['morph_flower'],
            $img_data['morph_branch'],
            $id
        );
        $stmt->execute();

        header("Location: view_mango.php?id=$id");
        exit;
    }

    // ตัวเลือกหมวดหมู่ (radio)
    $category_options = ['เชิงพาณิชย์', 'เชิงอนุรักษ์', 'ครัวเรือน'];

    // ตัวเลือกการขยายพันธุ์ (checkbox)
    $propagation_options = ['การเพาะจากเมล็ด', 'ทาบกิ่ง', 'เสียบยอด'];

    // ตัวเลือกการแปรรูป (checkbox)
    $processing_options = ['การทำแห้ง', 'การแปรรูป', 'การบรรจุหีบห่อ'];

    // แปลงค่าที่เก็บในฐานข้อมูลเป็น array
    $propagation_arr = !empty($mango['propagation']) ? explode(',', $mango['propagation']) : [];
    $processing_arr = !empty($mango['processing']) ? explode(',', $mango['processing']) : [];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลมะม่วง</title>
    
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
            padding: 30px;
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
            font-size: 3rem;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .form-section {
            background: var(--white);
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
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
        
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
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
        
        .image-preview-container {
            position: relative;
            margin-bottom: 15px;
        }
        
        .image-preview {
            width: 100%;
            height: 180px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: var(--light-bg);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .image-preview:hover {
            border-color: var(--primary-color);
        }
        
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        
        .preview-icon {
            font-size: 3rem;
            color: #ddd;
        }
        
        .preview-text {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .image-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-upload {
            flex: 1;
            background: var(--primary-light);
            color: var(--primary-dark);
            border: none;
            border-radius: 6px;
            padding: 8px 15px;
            font-size: 0.9rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-upload:hover {
            background: var(--primary-color);
            color: white;
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
        
        .morphology-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .morphology-item {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .header-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            
            .mango-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="mango-header position-relative">
            <div class="d-flex align-items-center">
                <div class="header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h1 class="mb-3">แก้ไขข้อมูลมะม่วง</h1>
                    <p class="mb-0"><?= htmlspecialchars($mango['name_local']) ?> (<?= htmlspecialchars($mango['name_sci']) ?>)</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-start mb-4">
            <a href="mango_list.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> กลับสู่รายการมะม่วง
            </a>
        </div>
        
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- คอลัมน์ 1: ข้อมูลทั่วไป -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            ข้อมูลทั่วไป
                        </h3>
                        
                        <div class="form-group">
                            <label class="form-label required">ชื่อวิทยาศาสตร์</label>
                            <input type="text" name="name_sci" class="form-control" value="<?= htmlspecialchars($mango['name_sci']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">ชื่อท้องถิ่น</label>
                            <input type="text" name="name_local" class="form-control" value="<?= htmlspecialchars($mango['name_local']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ลักษณะของดินที่เหมาะสม</label>
                            <textarea name="soil" class="form-control" rows="4"><?= htmlspecialchars($mango['soil']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ระยะเวลาเพาะปลูก</label>
                            <textarea name="planting_period" class="form-control" rows="4"><?= htmlspecialchars($mango['planting_period']) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- คอลัมน์ 2: การขยายพันธุ์ + หมวดหมู่ + การประมวลผล -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="form-section mb-4">
                        <h3 class="section-title">
                            <i class="fas fa-seedling"></i>
                            การขยายพันธุ์
                        </h3>
                        
                        <?php foreach ($propagation_options as $opt): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="propagation[]" id="prop_<?= $opt ?>" value="<?= $opt ?>"
                                    <?= in_array($opt, $propagation_arr) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="prop_<?= $opt ?>"><?= $opt ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="form-section mb-4">
                        <h3 class="section-title">
                            <i class="fas fa-tags"></i>
                            หมวดหมู่
                        </h3>
                        
                        <?php foreach ($category_options as $cat): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="cat_<?= $cat ?>" value="<?= $cat ?>"
                                    <?= ($mango['category'] === $cat) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat_<?= $cat ?>"><?= $cat ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-cogs"></i>
                            การประมวลผล
                        </h3>
                        
                        <?php foreach ($processing_options as $opt): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="processing[]" id="proc_<?= $opt ?>" value="<?= $opt ?>"
                                    <?= in_array($opt, $processing_arr) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="proc_<?= $opt ?>"><?= $opt ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- คอลัมน์ 3: รูปภาพ -->
                <div class="col-md-12">
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-images"></i>
                            รูปภาพมะม่วง
                        </h3>
                        <div class="container-fluid px-0">
                            <div class="row g-3">
                                <!-- แถวที่ 1 -->
                                <div class="col-md-4">
                                    <label class="form-label">รูปภาพหลัก</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="headerPreview">
                                            <img 
                                                src="<?= $mango['header_img'] ? '/Data_mango/' . htmlspecialchars($mango['header_img']) : '' ?>" 
                                                alt="Preview" 
                                                style="<?= $mango['header_img'] ? 'display:block;' : 'display:none;' ?>">
                                            <?php if (!$mango['header_img']): ?>
                                                <div class="preview-icon">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                                <div class="preview-text">รูปภาพหลักมะม่วง</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-actions">
                                            <label class="btn-upload">
                                                <i class="fas fa-upload me-2"></i>อัพโหลด
                                                <input type="file" name="header_img" class="d-none" accept="image/*" onchange="previewImage(this, 'headerPreview')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ผล</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="fruitPreview">
                                            <img 
                                                src="<?= $mango['morph_fruit'] ? '/Data_mango/' . htmlspecialchars($mango['morph_fruit']) : '' ?>" 
                                                alt="Preview" 
                                                style="<?= $mango['morph_fruit'] ? 'display:block;' : 'display:none;' ?>">
                                            <?php if (!$mango['morph_fruit']): ?>
                                                <div class="preview-icon">
                                                    <i class="fas fa-apple-alt"></i>
                                                </div>
                                                <div class="preview-text">รูปผลมะม่วง</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-actions">
                                            <label class="btn-upload">
                                                <i class="fas fa-upload me-2"></i>อัพโหลด
                                                <input type="file" name="morph_fruit" class="d-none" accept="image/*" onchange="previewImage(this, 'fruitPreview')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ต้น</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="treePreview">
                                            <img 
                                                src="<?= $mango['morph_tree'] ? '/Data_mango/' . htmlspecialchars($mango['morph_tree']) : '' ?>" 
                                                alt="Preview" 
                                                style="<?= $mango['morph_tree'] ? 'display:block;' : 'display:none;' ?>">
                                            <?php if (!$mango['morph_tree']): ?>
                                                <div class="preview-icon">
                                                    <i class="fas fa-tree"></i>
                                                </div>
                                                <div class="preview-text">รูปต้นมะม่วง</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-actions">
                                            <label class="btn-upload">
                                                <i class="fas fa-upload me-2"></i>อัพโหลด
                                                <input type="file" name="morph_tree" class="d-none" accept="image/*" onchange="previewImage(this, 'treePreview')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <!-- แถวที่ 2 -->
                                <div class="col-md-4">
                                    <label class="form-label">ใบ</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="leafPreview">
                                            <img 
                                                src="<?= $mango['morph_leaf'] ? '/Data_mango/' . htmlspecialchars($mango['morph_leaf']) : '' ?>" 
                                                alt="Preview" 
                                                style="<?= $mango['morph_leaf'] ? 'display:block;' : 'display:none;' ?>">
                                            <?php if (!$mango['morph_leaf']): ?>
                                                <div class="preview-icon">
                                                    <i class="fas fa-leaf"></i>
                                                </div>
                                                <div class="preview-text">รูปใบมะม่วง</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-actions">
                                            <label class="btn-upload">
                                                <i class="fas fa-upload me-2"></i>อัพโหลด
                                                <input type="file" name="morph_leaf" class="d-none" accept="image/*" onchange="previewImage(this, 'leafPreview')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ดอก</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="flowerPreview">
                                            <img 
                                                src="<?= $mango['morph_flower'] ? '/Data_mango/' . htmlspecialchars($mango['morph_flower']) : '' ?>" 
                                                alt="Preview" 
                                                style="<?= $mango['morph_flower'] ? 'display:block;' : 'display:none;' ?>">
                                            <?php if (!$mango['morph_flower']): ?>
                                                <div class="preview-icon">
                                                    <i class="fas fa-spa"></i>
                                                </div>
                                                <div class="preview-text">รูปดอกมะม่วง</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-actions">
                                            <label class="btn-upload">
                                                <i class="fas fa-upload me-2"></i>อัพโหลด
                                                <input type="file" name="morph_flower" class="d-none" accept="image/*" onchange="previewImage(this, 'flowerPreview')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- กิ่ง -->
                                <div class="col-md-4">
                                    <label class="form-label">กิ่ง</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="branchPreview">
                                            <img 
                                                src="<?= $mango['morph_branch'] ? '/Data_mango/' . htmlspecialchars($mango['morph_branch']) : '' ?>" 
                                                alt="Preview" 
                                                style="<?= $mango['morph_branch'] ? 'display:block;' : 'display:none;' ?>">
                                            <?php if (!$mango['morph_branch']): ?>
                                                <div class="preview-icon">
                                                    <i class="fas fa-tree"></i>
                                                </div>
                                                <div class="preview-text">รูปกิ่งมะม่วง</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-actions">
                                            <label class="btn-upload">
                                                <i class="fas fa-upload me-2"></i>อัพโหลด
                                                <input type="file" name="morph_branch" class="d-none" accept="image/*" onchange="previewImage(this, 'branchPreview')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ปุ่มดำเนินการ -->
                <div class="row">
                    <div class="col-12 text-center my-5">
                        <button type="submit" class="btn btn-submit me-3 px-4 py-2">
                            <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                        </button>
                        <button onclick="location.href='view_mango.php?id=<?= $id ?>'" type="button" class="btn btn-back me-3 px-4 py-2">
                            <i class="fas fa-times me-2"></i> ยกเลิก
                        </button>
                    </div>
                </div>
        </form>
    </div>

    <!-- Bootstrap & jQuery Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ฟังก์ชันแสดงตัวอย่างรูปภาพ
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const img = preview.querySelector('img');
            const icon = preview.querySelector('.preview-icon');
            const text = preview.querySelector('.preview-text');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // สร้าง element img ถ้ายังไม่มี
                    if (!img) {
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = "Preview";
                        preview.innerHTML = '';
                        preview.appendChild(newImg);
                    } else {
                        img.src = e.target.result;
                        img.style.display = 'block';
                    }
                    
                    if (icon) icon.style.display = 'none';
                    if (text) text.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                if (img) img.style.display = 'none';
                if (icon) icon.style.display = 'flex';
                if (text) text.style.display = 'block';
            }
        }
        
        // ฟังก์ชันเพิ่มเอฟเฟกต์เมื่อโหลดหน้า
        $(document).ready(function() {
            $('.form-section').each(function(i) {
                $(this).css('opacity', '0');
                setTimeout(() => {
                    $(this).css({
                        'opacity': '1',
                        'transform': 'translateY(0)'
                    });
                }, 200 * i);
            });
        });
    </script>
</body>
</html>