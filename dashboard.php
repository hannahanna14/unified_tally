<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
include 'includes/header.php';

$today = date('Y-m-d');
$month = date('Y-m');

function getCount($db, $condition = "1=1", $params = []) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM tally_records WHERE $condition");
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

// Queries for today's statistics
$total_today = getCount($db, "DATE(created_at) = ?", [$today]);
$total_month = getCount($db, "strftime('%Y-%m', created_at) = ?", [$month]);

$male_count = getCount($db, "DATE(created_at) = ? AND gender = 'Male'", [$today]);
$female_count = getCount($db, "DATE(created_at) = ? AND gender = 'Female'", [$today]);

$opd_count = getCount($db, "DATE(created_at) = ? AND point_of_entry = 'OPD'", [$today]);
$er_count = getCount($db, "DATE(created_at) = ? AND point_of_entry = 'ER'", [$today]);
$ipd_count = getCount($db, "DATE(created_at) = ? AND point_of_entry = 'IPD'", [$today]);

// Fetch Top Referral Source for today
$stmt = $db->prepare("SELECT referral_source, COUNT(*) as count FROM tally_records WHERE DATE(created_at) = ? AND referral_source != '' GROUP BY referral_source ORDER BY count DESC LIMIT 1");
$stmt->execute([$today]);
$top_referral = $stmt->fetch(PDO::FETCH_ASSOC);
$top_referral_text = $top_referral ? $top_referral['referral_source'] : 'None Yet';

// Greeting based on time
$hour = date('H');
if ($hour < 12) $greeting = "Good Morning";
elseif ($hour < 18) $greeting = "Good Afternoon";
else $greeting = "Good Evening";

$username = $_SESSION['username'] ?? 'User';
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="margin-bottom: 5px;"><?php echo $greeting; ?>, <span style="color: var(--primary); text-transform: capitalize;"><?php echo htmlspecialchars($username); ?>!</span></h1>
        <p style="color: var(--text-muted); margin: 0;">Here is what's happening today at MOPH - Manticao.</p>
    </div>
    <div style="text-align: right; background: white; padding: 12px 20px; border-radius: 8px; border: 1px solid var(--border); box-shadow: var(--card-shadow);">
        <p style="margin: 0; font-weight: 600; color: var(--text-dark);"><?php echo date('l, F j, Y'); ?></p>
        <p style="margin: 0; font-size: 0.9rem; color: var(--primary); font-weight: 500;"><?php echo date('h:i A'); ?> (PHT)</p>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Main Focus: Today's Total -->
    <div class="stat-card" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: white; border: none; display: flex; flex-direction: column; justify-content: center;">
        <h3 style="color: rgba(255,255,255,0.8);">Total Patients Today</h3>
        <div class="value" style="color: white; font-size: 3.5rem; line-height: 1; margin: 10px 0;"><?php echo $total_today; ?></div>
        <p style="margin: 0; font-size: 0.85rem; color: rgba(255,255,255,0.9);">
            Total this month: <strong><?php echo $total_month; ?></strong>
        </p>
    </div>

    <!-- Demographics -->
    <div class="stat-card">
        <h3>Gender Breakdown</h3>
        <div style="display: flex; justify-content: space-around; margin-top: 20px;">
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #0284c7; line-height: 1;"><?php echo $male_count; ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Male</div>
            </div>
            <div style="width: 1px; background: var(--border);"></div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #db2777; line-height: 1;"><?php echo $female_count; ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Female</div>
            </div>
        </div>
    </div>

    <!-- Entry Points -->
    <div class="stat-card">
        <h3>Point of Entry</h3>
        <div style="display: flex; justify-content: space-around; margin-top: 20px;">
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); line-height: 1;"><?php echo $opd_count; ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">OPD</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); line-height: 1;"><?php echo $er_count; ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">ER</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); line-height: 1;"><?php echo $ipd_count; ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">IPD</div>
            </div>
        </div>
    </div>

    <!-- Highlights -->
    <div class="stat-card">
        <h3>Top Referral Today</h3>
        <div class="value" style="font-size: 1.6rem; margin-top: 20px; color: #d97706;"><?php echo htmlspecialchars($top_referral_text); ?></div>
        <p style="margin: 8px 0 0 0; font-size: 0.85rem; color: var(--text-muted);">Most frequent source</p>
    </div>
</div>

<div class="card" style="margin-top: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.2rem;">Recent Encodes</h2>
        <a href="encode.php" class="btn" style="padding: 8px 15px; font-size: 0.9rem;">+ New Record</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Time (PHT)</th>
                <th>Entry Point</th>
                <th>Gender</th>
                <th>Class</th>
                <th>Address</th>
                <th>Referral Source</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $db->query("SELECT * FROM tally_records ORDER BY id DESC LIMIT 5");
            $has_records = false;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $has_records = true;
                echo "<tr>";
                echo "<td style='color: var(--text-muted); font-size: 0.9rem;'>" . date('h:i A', strtotime($row['created_at'])) . "</td>";
                echo "<td style='font-weight: 500;'>" . htmlspecialchars($row['point_of_entry']) . "</td>";
                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                
                // Format class
                $class_display = $row['classification'];
                if ($class_display == 'B1') $class_display = 'B&#8321;';
                if ($class_display == 'C1') $class_display = 'C&#8321;';
                if ($class_display == 'C2') $class_display = 'C&#8322;';
                if ($class_display == 'C3') $class_display = 'C&#8323;';
                
                echo "<td><span style='background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--border); color: var(--text-dark);'>" . $class_display . "</span></td>";
                echo "<td>" . htmlspecialchars($row['address'] ? $row['address'] : '-') . "</td>";
                echo "<td>" . htmlspecialchars($row['referral_source']) . "</td>";
                echo "</tr>";
            }
            
            if (!$has_records) {
                echo "<tr><td colspan='6' style='text-align: center; color: var(--text-muted); padding: 30px;'>No records found for today. Get started by clicking '+ New Record'.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
