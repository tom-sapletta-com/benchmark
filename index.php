<?php
/**
 * Benchmark Results Comparison Server
 * 
 * This script handles benchmark result uploads and provides a comparison interface
 * for benchmark.sapletta.com
 */

// Configuration
$upload_dir = 'uploads/';
$max_file_size = 1024 * 1024; // 1MB
$allowed_extensions = ['csv'];
$site_title = 'Benchmark Comparison - benchmark.sapletta.com';

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Handle file upload
$upload_message = '';
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (isset($_FILES['benchmark_file']) && $_FILES['benchmark_file']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['benchmark_file']['tmp_name'];
        $name = $_FILES['benchmark_file']['name'];
        $size = $_FILES['benchmark_file']['size'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        // Validate file
        if ($size > $max_file_size) {
            $upload_message = "Error: File size exceeds the maximum limit of 1MB.";
        } elseif (!in_array($ext, $allowed_extensions)) {
            $upload_message = "Error: Only CSV files are allowed.";
        } else {
            // Generate unique filename
            $timestamp = date('YmdHis');
            $unique_name = $timestamp . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', basename($name, '.' . $ext)) . '.' . $ext;
            $upload_path = $upload_dir . $unique_name;
            
            // Move file to upload directory
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $uploaded_file = $unique_name;
                $result_url = 'https://benchmark.sapletta.com/?view=' . urlencode($unique_name);
                
                // If API request, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'url' => $result_url]);
                    exit;
                }
                
                $upload_message = "success: File uploaded successfully. <a href=\"$result_url\">View results</a>";
            } else {
                $upload_message = "Error: Failed to upload file.";
            }
        }
    } else {
        $upload_message = "Error: No file uploaded or upload error occurred.";
    }
    
    // If API request, return JSON response for errors too
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $upload_message]);
        exit;
    }
}

// Get list of available benchmark files
function get_benchmark_files() {
    global $upload_dir;
    $files = [];
    
    if ($handle = opendir($upload_dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && pathinfo($entry, PATHINFO_EXTENSION) === 'csv') {
                $files[] = $entry;
            }
        }
        closedir($handle);
    }
    
    // Sort by timestamp (newest first)
    usort($files, function($a, $b) {
        return strcmp($b, $a);
    });
    
    return $files;
}

// Parse CSV file
function parse_csv($file_path) {
    $data = [];
    
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        // Read header row
        $header = fgetcsv($handle, 1000, ",");
        
        // Read data rows
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $item = [];
            foreach ($header as $i => $key) {
                $item[$key] = isset($row[$i]) ? $row[$i] : '';
            }
            $data[] = $item;
        }
        fclose($handle);
    }
    
    return $data;
}

// Get system info from benchmark data
function get_system_info($data) {
    if (empty($data)) {
        return [];
    }
    
    // All rows have the same system info
    $first_row = $data[0];
    return [
        'system' => $first_row['system'] ?? 'Unknown',
        'cpu_model' => $first_row['cpu_model'] ?? 'Unknown',
        'kernel' => $first_row['kernel'] ?? 'Unknown',
        'gpu' => $first_row['gpu'] ?? 'Unknown',
        'ram_total' => $first_row['ram_total'] ?? 'Unknown',
        'disks' => $first_row['disks'] ?? 'Unknown',
        'data' => $first_row['data'] ?? 'Unknown',
    ];
}

// Get benchmark results
function get_benchmark_results($data) {
    $results = [];
    
    foreach ($data as $row) {
        $results[$row['test']] = [
            'value' => $row['wartosc'],
            'unit' => $row['jednostka']
        ];
    }
    
    return $results;
}

// View mode - display specific benchmark or comparison
$view_files = [];
$comparison_data = [];

if (isset($_GET['view'])) {
    $view_files[] = $_GET['view'];
}

if (isset($_GET['compare'])) {
    $compare_files = explode(',', $_GET['compare']);
    foreach ($compare_files as $file) {
        if (!in_array($file, $view_files)) {
            $view_files[] = $file;
        }
    }
}

// If no specific files are requested, show the most recent one
if (empty($view_files)) {
    $all_files = get_benchmark_files();
    if (!empty($all_files)) {
        $view_files[] = $all_files[0];
    }
}

// Load data for the requested files
foreach ($view_files as $file) {
    $file_path = $upload_dir . $file;
    if (file_exists($file_path)) {
        $data = parse_csv($file_path);
        $system_info = get_system_info($data);
        $results = get_benchmark_results($data);
        
        $comparison_data[] = [
            'file' => $file,
            'system_info' => $system_info,
            'results' => $results
        ];
    }
}

// Get all available benchmark files for selection
$all_benchmark_files = get_benchmark_files();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2, h3 {
            color: #333;
        }
        .container {
            margin-bottom: 30px;
        }
        .upload-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        .system-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .chart-container {
            max-width: 800px;
            margin: 20px auto;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #45a049;
        }
        .file-list {
            margin: 20px 0;
        }
        .file-item {
            padding: 10px;
            margin: 5px 0;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .file-item a {
            color: #333;
            text-decoration: none;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
        .comparison-controls {
            margin: 20px 0;
        }
        .comparison-controls select {
            padding: 8px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1><?php echo $site_title; ?></h1>
    
    <div class="container">
        <div class="upload-form">
            <h2>Upload Benchmark Results</h2>
            <?php if (!empty($upload_message)): ?>
                <div class="message <?php echo strpos($upload_message, 'Error') === 0 ? 'error' : 'success'; ?>">
                    <?php echo $upload_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                <input type="file" name="benchmark_file" accept=".csv" required>
                <button type="submit" class="btn">Upload</button>
            </form>
            <p>Or use the <code>publish.sh</code> script from the benchmark tool to upload results automatically.</p>
        </div>
        
        <?php if (!empty($comparison_data)): ?>
            <div class="chart-container">
                <canvas id="radarChart"></canvas>
            </div>
            
            <div class="comparison-controls">
                <h3>Compare with another benchmark</h3>
                <form action="" method="get">
                    <select name="compare">
                        <?php foreach ($all_benchmark_files as $file): ?>
                            <?php if (!in_array($file, $view_files)): ?>
                                <option value="<?php echo htmlspecialchars($file); ?>"><?php echo htmlspecialchars($file); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php foreach ($view_files as $file): ?>
                        <input type="hidden" name="view" value="<?php echo htmlspecialchars($file); ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="btn">Compare</button>
                </form>
            </div>
            
            <h2>Benchmark Results</h2>
            
            <table>
                <tr>
                    <th>Test</th>
                    <?php foreach ($comparison_data as $data): ?>
                        <th><?php echo htmlspecialchars(basename($data['file'], '.csv')); ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php
                $test_keys = ['CPU_total_time', 'RAM_transfer_rate', 'Disk_write_speed', 'GPU_glmark2_score'];
                foreach ($test_keys as $test): ?>
                    <tr>
                        <td><?php echo str_replace('_', ' ', $test); ?></td>
                        <?php foreach ($comparison_data as $data): ?>
                            <td>
                                <?php if (isset($data['results'][$test])): ?>
                                    <?php echo htmlspecialchars($data['results'][$test]['value']); ?> 
                                    <?php echo htmlspecialchars($data['results'][$test]['unit']); ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <?php foreach ($comparison_data as $index => $data): ?>
                <div class="system-info">
                    <h3>System Information: <?php echo htmlspecialchars(basename($data['file'], '.csv')); ?></h3>
                    <table>
                        <?php foreach ($data['system_info'] as $key => $value): ?>
                            <tr>
                                <th><?php echo ucfirst($key); ?></th>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="file-list">
            <h2>Recent Benchmark Results</h2>
            <?php if (empty($all_benchmark_files)): ?>
                <p>No benchmark results available.</p>
            <?php else: ?>
                <?php foreach (array_slice($all_benchmark_files, 0, 10) as $file): ?>
                    <div class="file-item">
                        <a href="?view=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($comparison_data)): ?>
    <script>
        // Prepare data for radar chart
        const chartData = {
            labels: ['CPU total time', 'RAM transfer rate', 'Disk write speed', 'GPU glmark2 score'],
            datasets: []
        };
        
        const colors = [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)'
        ];
        
        const borderColors = colors.map(c => c.replace('0.5', '1'));
        
        <?php foreach ($comparison_data as $index => $data): ?>
        chartData.datasets.push({
            label: '<?php echo addslashes(basename($data['file'], '.csv')); ?>',
            data: [
                <?php
                // CPU time - lower is better, so invert
                $cpu_time = isset($data['results']['CPU_total_time']) ? floatval($data['results']['CPU_total_time']['value']) : 0;
                echo $cpu_time > 0 ? (10 / $cpu_time) : 'null';
                ?>,
                <?php
                // RAM transfer - higher is better
                echo isset($data['results']['RAM_transfer_rate']) ? floatval($data['results']['RAM_transfer_rate']['value']) : 'null';
                ?>,
                <?php
                // Disk write - higher is better
                echo isset($data['results']['Disk_write_speed']) ? floatval($data['results']['Disk_write_speed']['value']) : 'null';
                ?>,
                <?php
                // GPU score - higher is better
                echo isset($data['results']['GPU_glmark2_score']) ? floatval($data['results']['GPU_glmark2_score']['value']) : 'null';
                ?>
            ],
            backgroundColor: colors[<?php echo $index; ?> % colors.length],
            borderColor: borderColors[<?php echo $index; ?> % borderColors.length],
            borderWidth: 2,
            fill: true,
            pointRadius: 3
        });
        <?php endforeach; ?>
        
        // Create radar chart
        const ctx = document.getElementById('radarChart').getContext('2d');
        const radarChart = new Chart(ctx, {
            type: 'radar',
            data: chartData,
            options: {
                scales: {
                    r: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Benchmark Comparison'
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
