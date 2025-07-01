<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการข้อมูลมะม่วง - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
            --accent-color: #ff9800;
            --light-bg: #f8f9fa;
            --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }
        
        .dashboard-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.05);
            padding: 20px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .logo {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .sidebar .logo img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: #555;
            padding: 12px 25px;
            margin: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
            transition: margin 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
        }
        
        .sidebar .nav-link.active {
            font-weight: 600;
        }
        
        .main-content {
            padding: 25px;
        }
        
        .page-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            margin-right: 15px;
            font-size: 1.8rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 25px;
            transition: all 0.3s;
            border-left: 4px solid var(--accent-color);
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-card::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: rgba(76, 175, 80, 0.1);
            border-radius: 0 0 0 100%;
        }
        
        .stats-card i {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 15px;
        }
        
        .stats-card .number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .stats-card .label {
            color: #666;
            font-size: 1rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-title {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }
        
        .mango-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 25px;
            height: 100%;
        }
        
        .mango-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .mango-img {
            height: 180px;
            background-size: cover;
            background-position: center;
        }
        
        .mango-info {
            padding: 20px;
        }
        
        .mango-name {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0 0 10px;
            font-size: 1.2rem;
        }
        
        .mango-sci-name {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .mango-category {
            background: rgba(76, 175, 80, 0.1);
            color: var(--primary-color);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .mango-details {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .mango-details li {
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
        }
        
        .mango-details li i {
            color: var(--accent-color);
            margin-right: 10px;
            width: 18px;
        }
        
        .btn-add-mango {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3);
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
        }
        
        .btn-add-mango i {
            margin-right: 8px;
        }
        
        .btn-add-mango:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(46, 125, 50, 0.4);
            color: white;
        }
        
        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(76, 175, 80, 0.1);
            color: var(--primary-color);
            transition: all 0.3s;
            border: none;
        }
        
        .action-btn:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .recent-mangos {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 25px;
        }
        
        .table-mangos {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-mangos th {
            background: rgba(76, 175, 80, 0.1);
            color: var(--primary-color);
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .table-mangos td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .table-mangos tr:hover {
            background-color: rgba(76, 175, 80, 0.05);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-published {
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
        }
        
        .status-draft {
            background: rgba(255, 152, 0, 0.1);
            color: var(--accent-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .stats-card .number {
                font-size: 1.8rem;
            }
        }
        
        /* ส่วนแก้ไขสำหรับ Sidebar Responsive */
        @media (max-width: 767.98px) {
            #sidebarNav {
                flex: 0 0 60px !important;
                width: 60px !important;
                min-width: 60px !important;
                max-width: 60px !important;
                padding: 10px 0 !important;
            }
            #sidebarNav .logo {
                padding: 10px 0 !important;
            }
            #sidebarNav .logo img {
                width: 40px !important;
                height: 40px !important;
                margin-bottom: 5px !important;
            }
            #sidebarNav .logo h4 {
                display: none !important;
            }
            #sidebarNav .nav-link {
                padding: 12px 5px !important;
                margin: 5px 5px !important;
                justify-content: center !important;
            }
            #sidebarNav .nav-link i {
                margin-right: 0 !important;
            }
            #sidebarNav .nav-link span {
                display: none !important;
            }
        }
        
        @media (min-width: 768px) and (max-width: 991.98px) {
            #sidebarNav .logo h4 {
                display: none !important;
            }
            #sidebarNav {
                min-width: 80px;
                max-width: 80px;
            }
            #sidebarNav .nav-link {
                padding: 12px 10px !important;
                margin: 5px 5px !important;
            }
            #sidebarNav .nav-link i {
                margin-right: 0 !important;
            }
            #sidebarNav .nav-link span {
                display: none !important;
            }
        }
        
        @media (max-width: 991.98px) {
        #sidebarNav {
            display: none !important;
        }
        #mobileNav {
            display: block !important;
        }
        }
        @media (min-width: 992px) {
            #mobileNav {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    
    <header class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0"><i class="fas fa-leaf"></i> ระบบจัดการข้อมูลมะม่วง</h1>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <div class="user-info me-4">
                        <div class="user-avatar">AD</div>
                        <div>
                            <div class="fw-bold">ผู้ดูแลระบบ</div>
                            <div class="small">Admin</div>
                        </div>
                    </div>
                    <button class="action-btn me-2">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="action-btn">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-lg-2 col-md-3 sidebar px-0 position-relative d-none d-md-block" id="sidebarNav">
                <div class="logo text-center py-3 border-bottom">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNTYgMjU2Ij48cGF0aCBmaWxsPSIjMkU3RDMzIiBkPSJNMjMxLjkgMTYwQTIwLjMgMjAuMyAwIDAgMCAyMjQgMTI4YzAtMTIuNy05LjEtMjMuMi0yMS4yLTI1LjZhNDAgNDAgMCAwIDAtNzcuNiAwQzExMy4xIDEwNC44IDEwNCAxMTUuMyAxMDQgMTI4YTIwLjMgMjAuMyAwIDAgMC03LjkgMzJjLTkuOSAyLjYtMTcuMiAxMS42LTE3LjIgMjEuOGEyMCAyMCAwIDAgMCAyMCAyMGgxNDJhMjAgMjAgMCAwIDAgMjAtMjBjMC0xMC4yLTcuMy0xOS4yLTE3LjEtMjEuOHoiLz48cGF0aCBmaWxsPSIjRkZDNzBGIiBkPSJNMTM2IDI0YTI0IDI0IDAgMSAwIDI0IDI0YTI0IDI0IDAgMCAwLTI0LTI0em0wIDQ4YTI0IDI0IDAgMSAwIDI0IDI0YTI0IDI0IDAgMCAwLTI0LTI0em00OCAwYTI0IDI0IDAgMSAwIDI0IDI0YTI0IDI0IDAgMCAwLTI0LTI0eiIvPjxwYXRoIGZpbGw9IiNGRjlCMDAiIGQ9Ik0yMzEuOSAxNjBIMjRjMTQuOS0xOC45IDQ2LjYtMzIgODAtMzJoNDhjMzMuNCAwIDY1LjEgMTMuMSA4MCAzMnoiLz48L3N2Zz4=" alt="Mango Logo">
                    <h4 class="mb-0 d-none d-md-block">MangoDB</h4>
                </div>
                <ul class="nav flex-column text-center text-md-start">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" title="แดชบอร์ด">
                            <i class="fas fa-chart-line"></i>
                            <span class="d-none d-md-inline">แดชบอร์ด</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_mango.php" title="ข้อมูลมะม่วง">
                            <i class="fas fa-tree"></i>
                            <span class="d-none d-md-inline">ข้อมูลมะม่วง</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" title="การตั้งค่า">
                            <i class="fas fa-cog"></i>
                            <span class="d-none d-md-inline">การตั้งค่า</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" title="คู่มือ">
                            <i class="fas fa-book"></i>
                            <span class="d-none d-md-inline">คู่มือ</span>
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="#" title="ออกจากระบบ">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="d-none d-md-inline">ออกจากระบบ</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9 col-12 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="page-title">
                        <i class="fas fa-chart-line"></i> แดชบอร์ด
                    </h2>
                    <a href="add_mango_process.php" class="btn-add-mango">
                        <i class="fas fa-plus"></i> เพิ่มมะม่วงใหม่
                    </a>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-tree"></i>
                            <div class="number">142</div>
                            <div class="label">พันธุ์มะม่วงทั้งหมด</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-seedling"></i>
                            <div class="number">87</div>
                            <div class="label">พันธุ์เชิงพาณิชย์</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-leaf"></i>
                            <div class="number">32</div>
                            <div class="label">พันธุ์อนุรักษ์</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-home"></i>
                            <div class="number">23</div>
                            <div class="label">พันธุ์ครัวเรือน</div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts and Mango Cards -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">จำนวนมะม่วงแบ่งตามประเภท</h3>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        เดือนนี้
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">สัปดาห์นี้</a></li>
                                        <li><a class="dropdown-item" href="#">เดือนนี้</a></li>
                                        <li><a class="dropdown-item" href="#">ปีนี้</a></li>
                                    </ul>
                                </div>
                            </div>
                            <canvas id="mangoChart"></canvas>
                        </div>
                        
                        <div class="recent-mangos">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="chart-title">มะม่วงที่เพิ่มล่าสุด</h3>
                                <a href="#" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table-mangos">
                                    <thead>
                                        <tr>
                                            <th>ชื่อพันธุ์</th>
                                            <th>หมวดหมู่</th>
                                            <th>วันที่เพิ่ม</th>
                                            <th>สถานะ</th>
                                            <th>การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>มะม่วงน้ำดอกไม้</td>
                                            <td>เชิงพาณิชย์</td>
                                            <td>12 มิ.ย. 2566</td>
                                            <td><span class="status-badge status-published">เผยแพร่แล้ว</span></td>
                                            <td>
                                                <button class="action-btn me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>มะม่วงมันขุนศรี</td>
                                            <td>เชิงพาณิชย์</td>
                                            <td>10 มิ.ย. 2566</td>
                                            <td><span class="status-badge status-published">เผยแพร่แล้ว</span></td>
                                            <td>
                                                <button class="action-btn me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>มะม่วงยายกล่ำ</td>
                                            <td>อนุรักษ์</td>
                                            <td>8 มิ.ย. 2566</td>
                                            <td><span class="status-badge status-published">เผยแพร่แล้ว</span></td>
                                            <td>
                                                <button class="action-btn me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>มะม่วงแรด</td>
                                            <td>ครัวเรือน</td>
                                            <td>5 มิ.ย. 2566</td>
                                            <td><span class="status-badge status-draft">แบบร่าง</span></td>
                                            <td>
                                                <button class="action-btn me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>มะม่วงอกร่อง</td>
                                            <td>เชิงพาณิชย์</td>
                                            <td>3 มิ.ย. 2566</td>
                                            <td><span class="status-badge status-published">เผยแพร่แล้ว</span></td>
                                            <td>
                                                <button class="action-btn me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">การขยายพันธุ์ที่นิยม</h3>
                            </div>
                            <canvas id="propagationChart"></canvas>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-lg-12">
                                <div class="mango-card">
                                    <div class="mango-img" style="background-image: url('https://www.chiataigroup.com/imgadmins/detail_photo/Detail_th_1739849720.jpg');"></div>
                                    <div class="mango-info">
                                        <h3 class="mango-name">มะม่วงน้ำดอกไม้</h3>
                                        <div class="mango-sci-name">Mangifera indica 'Nam Dok Mai'</div>
                                        <span class="mango-category">เชิงพาณิชย์</span>
                                        <ul class="mango-details">
                                            <li><i class="fas fa-calendar"></i> เก็บเกี่ยว: มีนาคม-พฤษภาคม</li>
                                            <li><i class="fas fa-ruler-combined"></i> ขนาดผล: 12-16 ซม.</li>
                                            <li><i class="fas fa-weight"></i> น้ำหนัก: 300-400 กรัม</li>
                                            <li><i class="fas fa-tint"></i> ความหวาน: 16-18 °Brix</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-12 py-4">
                                <div class="mango-card">
                                    <div class="mango-img" style="background-image: url('https://cms.dmpcdn.com/food/2025/04/29/60f29630-24ca-11f0-a8ca-376cbf68f0bf_webp_original.webp');"></div>
                                    <div class="mango-info">
                                        <h3 class="mango-name">มะม่วงอกร่อง</h3>
                                        <div class="mango-sci-name">Mangifera indica 'Ok Rhong'</div>
                                        <span class="mango-category">เชิงพาณิชย์</span>
                                        <ul class="mango-details">
                                            <li><i class="fas fa-calendar"></i> เก็บเกี่ยว: เมษายน-มิถุนายน</li>
                                            <li><i class="fas fa-ruler-combined"></i> ขนาดผล: 10-14 ซม.</li>
                                            <li><i class="fas fa-weight"></i> น้ำหนัก: 250-350 กรัม</li>
                                            <li><i class="fas fa-tint"></i> ความหวาน: 18-22 °Brix</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom nav สำหรับ mobile -->
    <nav class="d-block d-md-none fixed-bottom bg-white border-top shadow-sm" id="mobileNav">
        <div class="d-flex justify-content-around py-2">
            <a href="#" class="text-success text-center" title="แดชบอร์ด"><i class="fas fa-chart-line fa-lg"></i></a>
            <a href="#" class="text-success text-center" title="ข้อมูลมะม่วง"><i class="fas fa-tree fa-lg"></i></a>
            <a href="#" class="text-success text-center" title="ผู้ใช้งาน"><i class="fas fa-users fa-lg"></i></a>
            <a href="#" class="text-success text-center" title="การตั้งค่า"><i class="fas fa-cog fa-lg"></i></a>
            <a href="#" class="text-success text-center" title="ออกจากระบบ"><i class="fas fa-sign-out-alt fa-lg"></i></a>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mango Type Chart
        const mangoCtx = document.getElementById('mangoChart').getContext('2d');
        const mangoChart = new Chart(mangoCtx, {
            type: 'bar',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.'],
                datasets: [{
                    label: 'เชิงพาณิชย์',
                    data: [12, 19, 15, 22, 18, 14],
                    backgroundColor: 'rgba(46, 125, 50, 0.7)',
                    borderColor: 'rgba(46, 125, 50, 1)',
                    borderWidth: 1
                }, {
                    label: 'อนุรักษ์',
                    data: [5, 8, 6, 10, 7, 9],
                    backgroundColor: 'rgba(255, 152, 0, 0.7)',
                    borderColor: 'rgba(255, 152, 0, 1)',
                    borderWidth: 1
                }, {
                    label: 'ครัวเรือน',
                    data: [3, 4, 2, 5, 6, 4],
                    backgroundColor: 'rgba(33, 150, 243, 0.7)',
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });
        
        // Propagation Chart
        const propCtx = document.getElementById('propagationChart').getContext('2d');
        const propChart = new Chart(propCtx, {
            type: 'doughnut',
            data: {
                labels: ['เสียบยอด', 'ทาบกิ่ง', 'เพาะเมล็ด', 'ตอนกิ่ง'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: [
                        'rgba(46, 125, 50, 0.8)',
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(139, 195, 74, 0.8)',
                        'rgba(205, 220, 57, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        
        // Simulate loading
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stats-card, .chart-container, .mango-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
    </script>
</body>
</html>