<?php

function getCatboxPrice() {
    $url = "https://api.dexscreener.com/latest/dex/pairs/ton/EQDq1cQKb6vQItJgBhwn7bS2FpJNnDLOKDUIBF8hHJgQwqlD";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['pair']['priceUsd'] ?? null;
}

function getToncoinPriceFromDexScreener() {
    $url = "https://api.dexscreener.com/latest/dex/pairs/bsc/0x737d75e160e68357337c76cf9617772a50690976";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['pair']['priceUsd'] ?? null;
}

function getExchangeRate() {
    $apiKey = "e3be7e74b6-bac779a256-sm0ppg"; 
    $url = "https://api.fastforex.io/fetch-one?from=USD&to=THB&api_key=$apiKey";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['result']['THB'] ?? null;
}

$totalDays = 60;

$catboxPriceUSD = getCatboxPrice();
$toncoinPriceUSD = getToncoinPriceFromDexScreener();
$usdToThbRate = getExchangeRate();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catboxPerDay = isset($_POST['catbox_per_day']) ? (float)$_POST['catbox_per_day'] : 0;

    if ($catboxPriceUSD && $usdToThbRate) {
        $dailyIncomeUSD = $catboxPerDay * $catboxPriceUSD;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายได้จาก Catbox</title>
    <style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
    h1 { text-align: center; color: #333; }
    form, .result { max-width: 600px; margin: 0 auto; padding: 20px; background: #fff; border-radius: 5px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
    label { display: block; margin-bottom: 5px; color: #555; }
    input[type="number"], input[type="submit"] { width: calc(100% - 10px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
    input[type="submit"] { background-color: #28a745; color: white; border: none; cursor: pointer; font-size: 16px; }
    input[type="submit"]:hover { background-color: #218838; }
    .result { margin-top: 20px; overflow-x: auto; }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 20px;
        table-layout: auto;
    }
    th, td {  
        font-size: 10px; 
        padding: 5px; 
        border: 1px solid #ddd; 
        text-align: center; 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
    }
    th { background-color: #f2f2f2; color: #333; font-weight: bold; }
    /* เพิ่มส่วนนี้เพื่อให้ข้อมูลใน td ไม่กลายเป็นลิงก์ */
    td a { 
        color: inherit; /* ใช้สีเดียวกับข้อความปกติ */
        text-decoration: none; /* ลบขีดเส้นใต้ */
        pointer-events: none; /* ปิดการทำงานของลิงก์ */
    }
    .info-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        max-width: 800px;
        margin: 20px auto; /* เพิ่มระยะห่างด้านบนและกึ่งกลางหน้า */
        margin-top: 40px; /* เพิ่มระยะห่างเฉพาะด้านบน */
    }
    .info-box {
        padding: 20px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>
</head>
<body>
    <h1>คำนวณรายได้จาก Catbox</h1>
    <form method="POST">
        <label for="catbox_per_day">แรงขุด Catbox ต่อวัน:</label>
        <input type="number" id="catbox_per_day" name="catbox_per_day" value="<?= isset($_POST['catbox_per_day']) ? htmlspecialchars($_POST['catbox_per_day']) : '' ?>" required min="0" step="0.000000001">
        <input type="submit" value="คำนวณ">
    </form>

<?php if (isset($dailyIncomeUSD) && $usdToThbRate): ?>
    <div class="result">
        <h2>ผลลัพธ์รายได้รายวัน</h2>
        <table>
    <tr>
        <th>วันที่</th>
        <th>เหรียญ CatBox</th>
        <th>เหรียญ Toncoin</th>
        <th>รายได้ (USD)</th>
        <th>รายได้ (THB)</th>
    </tr>
    <?php for ($day = 1; $day <= $totalDays; $day++): ?>
        <?php 
            $incomeUSD = $dailyIncomeUSD * $day;  
            $incomeTHB = $incomeUSD * $usdToThbRate; 
            $coinsEarnedCatbox = $catboxPerDay * $day;
            $coinsEarnedToncoin = ($dailyIncomeUSD / $toncoinPriceUSD) * $day; 
        ?>
        <tr>
            <td><?= $day ?></td>
            <td><?= number_format($coinsEarnedCatbox, 4) ?></td>
            <td><?= number_format($coinsEarnedToncoin, 4) ?></td>
            <td><?= number_format($incomeUSD, 2) ?> USD</td>
            <td><?= number_format($incomeTHB, 2) ?> THB</td>
        </tr>
    <?php endfor; ?>
</table>
    </div>
<?php endif; ?>

<?php if ($catboxPriceUSD && $toncoinPriceUSD && $usdToThbRate): ?>
    <div class="info-container">
      
        <div class="info-box">
            <strong>ราคา Catbox:</strong><br>
            <span>USD: <?= number_format($catboxPriceUSD, 4) ?> USD</span><br>
            <span>THB: <?= number_format($catboxPriceUSD * $usdToThbRate, 2) ?> บาท</span>
        </div>
        
        <div class="info-box">
            <strong>ราคา Toncoin:</strong><br>
            <span>USD: <?= number_format($toncoinPriceUSD, 4) ?> USD</span><br>
            <span>THB: <?= number_format($toncoinPriceUSD * $usdToThbRate, 2) ?> บาท</span>
        </div>
        
        <?php 
            $catboxPerToncoin = $toncoinPriceUSD / $catboxPriceUSD; 
            $toncoinPerCatbox = $catboxPriceUSD / $toncoinPriceUSD; 
        ?>
        <div class="info-box">
            <strong>อัตราส่วน:</strong><br>
            <span>1 Ton = <?= number_format($catboxPerToncoin, 4) ?> Catbox</span><br>
            <span>1 Catbox = <?= number_format($toncoinPerCatbox, 4) ?> Ton</span>
        </div>
        
        <div class="info-box">
            <strong>อัตราแลกเปลี่ยน USD/THB:</strong><br>
            <span><?= number_format($usdToThbRate, 2) ?></span>
        </div>

    </div>
<?php else: ?>
    <p>เซิฟเวอร์กำลังโหลดข้อมูลราคาเหรียญขัดข้อง กรุณารีเฟรชหรือรอสักครู่</p>
<?php endif; ?>
</body>
</html>
