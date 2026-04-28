<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form
    $point_of_entry = $_POST['point_of_entry'] ?? '';
    $referral_source = (isset($_POST['referral_source']) && $_POST['referral_source'] === 'Others') ? ($_POST['referral_source_custom'] ?? '') : ($_POST['referral_source'] ?? '');
    $classification = $_POST['classification'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $civil_status = (isset($_POST['civil_status']) && $_POST['civil_status'] === 'Others') ? ($_POST['civil_status_custom'] ?? '') : ($_POST['civil_status'] ?? '');
    $address = (isset($_POST['address']) && $_POST['address'] === 'Others') ? ($_POST['address_custom'] ?? '') : ($_POST['address'] ?? '');
    $occupation = (isset($_POST['occupation']) && $_POST['occupation'] === 'Others') ? ($_POST['occupation_custom'] ?? '') : ($_POST['occupation'] ?? '');
    $house = (isset($_POST['house']) && $_POST['house'] === 'Others') ? ($_POST['house_custom'] ?? '') : ($_POST['house'] ?? '');
    $light_source = $_POST['light_source'] ?? '';
    $water_source = $_POST['water_source'] ?? '';
    $educational_attainment = (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'Others') ? ($_POST['educational_attainment_custom'] ?? '') : ($_POST['educational_attainment'] ?? '');
    $household_members = (isset($_POST['household_members']) && $_POST['household_members'] === 'Others') ? ($_POST['household_members_custom'] ?? '') : ($_POST['household_members'] ?? '');

    if ($point_of_entry && $referral_source && $classification && $gender && $civil_status) {
        $stmt = $db->prepare("INSERT INTO tally_records (point_of_entry, referral_source, classification, gender, civil_status, address, occupation, house, light_source, water_source, educational_attainment, household_members) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$point_of_entry, $referral_source, $classification, $gender, $civil_status, $address, $occupation, $house, $light_source, $water_source, $educational_attainment, $household_members])) {
            $message = "Record encoded successfully!";
            $message_type = "success";
        } else {
            $message = "Error encoding record.";
            $message_type = "error";
        }
    } else {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    }
}

include 'includes/header.php';
?>

<h1>Encode Record</h1>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Column 1 -->
            <div>
                <div class="form-group">
                    <label for="point_of_entry">Point of Entry <span style="color:red">*</span></label>
                    <select name="point_of_entry" id="point_of_entry" required autofocus>
                        <option value="">Select...</option>
                        <option value="ER">ER</option>
                        <option value="OPD">OPD</option>
                        <option value="IPD">IPD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="referral_source">Source for Referral <span style="color:red">*</span></label>
                    <select name="referral_source" id="referral_source" onchange="toggleCustom(this, 'referral_source_custom')" required>
                        <option value="">Select...</option>
                        <option value="Lugait">Lugait</option>
                        <option value="Manticao">Manticao</option>
                        <option value="RHU Manticao">RHU Manticao</option>
                        <option value="Naawan">Naawan</option>
                        <option value="RHU Naawan">RHU Naawan</option>
                        <option value="Initao">Initao</option>
                        <option value="RHU Initao">RHU Initao</option>
                        <option value="OWWA">OWWA</option>
                        <option value="Libertad">Libertad</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="referral_source_custom" id="referral_source_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>

                <div class="form-group">
                    <label for="classification">Classification <span style="color:red">*</span></label>
                    <select name="classification" id="classification" required>
                        <option value="">Select...</option>
                        <option value="A">A</option>
                        <option value="B1">B&#8321;</option>
                        <option value="C1">C&#8321;</option>
                        <option value="C2">C&#8322;</option>
                        <option value="C3">C&#8323;</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="gender">Gender <span style="color:red">*</span></label>
                    <select name="gender" id="gender" required>
                        <option value="">Select...</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="civil_status">Civil Status <span style="color:red">*</span></label>
                    <select name="civil_status" id="civil_status" onchange="toggleCustom(this, 'civil_status_custom')" required>
                        <option value="">Select...</option>
                        <option value="Child">Child</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Common Law">Common Law</option>
                        <option value="Separated">Separated</option>
                        <option value="Widow">Widow</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="civil_status_custom" id="civil_status_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <select name="address" id="address" onchange="toggleCustom(this, 'address_custom')">
                        <option value="">Select...</option>
                        <option value="Lugait">Lugait</option>
                        <option value="Manticao">Manticao</option>
                        <option value="Naawan">Naawan</option>
                        <option value="Initao">Initao</option>
                        <option value="Opol">Opol</option>
                        <option value="Iligan">Iligan</option>
                        <option value="Libertad">Libertad</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="address_custom" id="address_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>
            </div>

            <!-- Column 2 -->
            <div>
                <div class="form-group">
                    <label for="occupation">Occupation</label>
                    <select name="occupation" id="occupation" onchange="toggleCustom(this, 'occupation_custom')">
                        <option value="">Select...</option>
                        <option value="Construction Worker">Construction Worker</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Barber">Barber</option>
                        <option value="Cook">Cook</option>
                        <option value="Driver">Driver</option>
                        <option value="Farmer">Farmer</option>
                        <option value="Nurse">Nurse</option>
                        <option value="Fishermen">Fishermen</option>
                        <option value="House Helper">House Helper</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="occupation_custom" id="occupation_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>

                <div class="form-group">
                    <label for="house">House</label>
                    <select name="house" id="house" onchange="toggleCustom(this, 'house_custom')">
                        <option value="">Select...</option>
                        <option value="Owned">Owned</option>
                        <option value="Rent">Rent</option>
                        <option value="Shared">Shared</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="house_custom" id="house_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>

                <div class="form-group">
                    <label for="light_source">Light Source</label>
                    <select name="light_source" id="light_source">
                        <option value="">Select...</option>
                        <option value="Electricity">Electricity</option>
                        <option value="Kerosene">Kerosene</option>
                        <option value="Solar">Solar</option>
                        <option value="Candle">Candle</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="water_source">Water Source</label>
                    <select name="water_source" id="water_source">
                        <option value="">Select...</option>
                        <option value="Water District">Water District</option>
                        <option value="Public Pump">Public Pump</option>
                        <option value="Deepwell">Deepwell</option>
                        <option value="Spring">Spring</option>
                        <option value="Public">Public</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="educational_attainment">Educational Attainment</label>
                    <select name="educational_attainment" id="educational_attainment" onchange="toggleCustom(this, 'educational_attainment_custom')">
                        <option value="">Select...</option>
                        <option value="Pre-school">Pre-school</option>
                        <option value="Elementary Level">Elementary Level</option>
                        <option value="Elementary Graduate">Elementary Graduate</option>
                        <option value="High School Level">High School Level</option>
                        <option value="High School Graduate">High School Graduate</option>
                        <option value="Senior High">Senior High</option>
                        <option value="College Level">College Level</option>
                        <option value="College Graduate">College Graduate</option>
                        <option value="Vocational">Vocational</option>
                        <option value="Post Graduate">Post Graduate</option>
                        <option value="Illiterate">Illiterate</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="educational_attainment_custom" id="educational_attainment_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>

                <div class="form-group">
                    <label for="household_members">Household Members</label>
                    <select name="household_members" id="household_members" onchange="toggleCustom(this, 'household_members_custom')">
                        <option value="">Select...</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="Others">Others (Custom)</option>
                    </select>
                    <input type="text" name="household_members_custom" id="household_members_custom" style="display:none; margin-top: 10px;" placeholder="Please specify">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-large" style="margin-top: 20px;">Save Record</button>
    </form>
</div>

<script>
function toggleCustom(selectElement, customInputId) {
    var customInput = document.getElementById(customInputId);
    if (selectElement.value === 'Others') {
        customInput.style.display = 'block';
        customInput.setAttribute('required', 'required');
        customInput.focus();
    } else {
        customInput.style.display = 'none';
        customInput.removeAttribute('required');
        customInput.value = '';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
