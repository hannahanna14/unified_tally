<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
include 'includes/header.php';

$filter_date = $_GET['date'] ?? date('Y-m-d');

function getCount($db, $condition = "1=1", $params = []) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM tally_records WHERE $condition");
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

// Dynamically group by any column and count occurrences!
function getCategoryBreakdown($db, $date, $category_column) {
    $stmt = $db->prepare("SELECT $category_column as name, COUNT(*) as count FROM tally_records WHERE DATE(created_at) = ? AND $category_column != '' AND $category_column IS NOT NULL GROUP BY $category_column ORDER BY count DESC, name ASC");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$total = getCount($db, "DATE(created_at) = ?", [$filter_date]);

// Fetch breakdowns for all fields automatically
$breakdowns = [
    'Point of Entry' => getCategoryBreakdown($db, $filter_date, 'point_of_entry'),
    'Gender' => getCategoryBreakdown($db, $filter_date, 'gender'),
    'Classification' => getCategoryBreakdown($db, $filter_date, 'classification'),
    'Source for Referral' => getCategoryBreakdown($db, $filter_date, 'referral_source'),
    'Address' => getCategoryBreakdown($db, $filter_date, 'address'),
    'Civil Status' => getCategoryBreakdown($db, $filter_date, 'civil_status'),
    'Occupation' => getCategoryBreakdown($db, $filter_date, 'occupation'),
    'Educational Attainment' => getCategoryBreakdown($db, $filter_date, 'educational_attainment'),
    'House' => getCategoryBreakdown($db, $filter_date, 'house'),
    'Light Source' => getCategoryBreakdown($db, $filter_date, 'light_source'),
    'Water Source' => getCategoryBreakdown($db, $filter_date, 'water_source'),
    'Household Members' => getCategoryBreakdown($db, $filter_date, 'household_members')
];

?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h1 style="margin-bottom: 5px;">Statistical Reports</h1>
        <p style="color: var(--text-muted); margin: 0;">Comprehensive breakdown for <?php echo date('F j, Y', strtotime($filter_date)); ?></p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="export.php" class="btn" style="background-color: #10b981; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);">
            <svg style="width:16px; height:16px; display:inline-block; vertical-align:middle; margin-right:5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export to Excel
        </a>
        <button onclick="window.print()" class="btn btn-secondary" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <svg style="width:16px; height:16px; display:inline-block; vertical-align:middle; margin-right:5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Report
        </button>
    </div>
</div>

<div class="card" style="background-color: var(--bg-color); border: none;">
    <form method="GET" action="" style="display: flex; gap: 15px; align-items: center; margin: 0;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="date" style="margin-right: 10px; font-weight: 600;">Filter by Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($filter_date); ?>" max="<?php echo date('Y-m-d'); ?>" onchange="this.form.submit()" style="width: auto; display: inline-block; padding: 10px 15px;">
        </div>
    </form>
</div>

<div id="printable-area">
    <!-- Print-only header -->
    <div style="text-align: center; margin-bottom: 30px; display: none;" class="print-header">
        <h2 style="margin-bottom: 5px; color: #000; font-size: 24px;">Unified Tally System - Daily Statistical Report</h2>
        <p style="margin: 0; color: #333; font-weight: bold; font-size: 16px;">MOPH - Manticao</p>
        <p style="margin: 8px 0 0 0; color: #555; font-size: 14px;">Date: <?php echo date('F j, Y', strtotime($filter_date)); ?></p>
        <hr style="border: 0; border-top: 1px solid #ccc; margin-top: 20px;">
    </div>

    <!-- Overview Card -->
    <div class="card" style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: white;">
        <h3 style="margin-top: 0; color: rgba(255,255,255,0.9); font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Total Encoded Patients</h3>
        <div style="font-size: 4rem; font-weight: 700; line-height: 1;"><?php echo $total; ?></div>
    </div>

    <!-- Grid for Breakdowns -->
    <div class="breakdown-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
        <?php foreach ($breakdowns as $title => $data): ?>
            <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; margin-bottom: 0;">
                <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid var(--border);">
                    <h3 style="margin: 0; font-size: 1.05rem; color: var(--primary);"><?php echo htmlspecialchars($title); ?></h3>
                </div>
                <div style="padding: 20px; flex: 1;">
                    <?php if (count($data) > 0): ?>
                        <table style="margin: 0;">
                            <tbody>
                                <?php foreach ($data as $row): ?>
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px dashed var(--border); font-weight: 500; color: var(--text-dark);">
                                            <?php 
                                            $name = $row['name'];
                                            if ($title === 'Classification') {
                                                if ($name == 'B1') $name = 'B&#8321;';
                                                if ($name == 'C1') $name = 'C&#8321;';
                                                if ($name == 'C2') $name = 'C&#8322;';
                                                if ($name == 'C3') $name = 'C&#8323;';
                                            }
                                            echo $name; 
                                            ?>
                                        </td>
                                        <td style="padding: 8px 0; border-bottom: 1px dashed var(--border); text-align: right; font-weight: 700; color: var(--primary); width: 60px;">
                                            <?php echo htmlspecialchars($row['count']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-muted); text-align: center; margin: 20px 0; font-style: italic; font-size: 0.9rem;">No data recorded</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
@media print {
    body { background-color: white !important; }
    .print-header { display: block !important; }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
        margin-bottom: 20px !important;
    }
    .card[style*="linear-gradient"] {
        background: none !important;
        color: black !important;
        border: 2px solid #000 !important;
        padding: 20px !important;
    }
    .card[style*="linear-gradient"] h3, .card[style*="linear-gradient"] div {
        color: black !important;
    }
    .breakdown-grid {
        display: block !important;
        column-count: 2;
        column-gap: 20px;
    }
    .breakdown-grid > div {
        display: inline-block; /* prevents card from breaking across columns */
        width: 100%;
        margin-bottom: 20px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
