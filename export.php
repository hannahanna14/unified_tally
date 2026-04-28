<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Set headers to force download as CSV (Excel format)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Unified_Tally_Export_' . date('Y-m-d') . '.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('ID', 'Date Encoded', 'Time Encoded', 'Point of Entry', 'Source for Referral', 'Classification', 'Gender', 'Civil Status', 'Address', 'Occupation', 'House', 'Light Source', 'Water Source', 'Educational Attainment', 'Household Members'));

// Fetch all data from the database
$stmt = $db->query("SELECT * FROM tally_records ORDER BY created_at DESC");

// Loop through the rows and output them to the CSV
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Format the classification so it looks normal in Excel (C1 instead of HTML subscript)
    $class = $row['classification'];
    
    fputcsv($output, array(
        $row['id'],
        date('Y-m-d', strtotime($row['created_at'])),
        date('h:i A', strtotime($row['created_at'])),
        $row['point_of_entry'],
        $row['referral_source'],
        $class,
        $row['gender'],
        $row['civil_status'],
        $row['address'],
        $row['occupation'],
        $row['house'],
        $row['light_source'],
        $row['water_source'],
        $row['educational_attainment'],
        $row['household_members']
    ));
}

fclose($output);
exit;
?>
