<?php
session_start();
require '../config/database.php';
require_once ('../TCPDF/tcpdf.php');
$pageTitle = "Report ";
if (!isset($_GET['parcel_id'])) {
    echo "Parcel ID is required.";
    exit;
}

$parcel_id = $_GET['parcel_id'];

$database = new Database();
$conn = $database->getConnection();

$query = "
SELECT 
    p.*, 
    s.first_name AS sender_first_name, 
    s.last_name AS sender_last_name, 
    s.email AS sender_email, 
    s.phone AS sender_phone, 
    s.address AS sender_address,
    r.first_name AS receiver_first_name, 
    r.last_name AS receiver_last_name, 
    r.email AS receiver_email, 
    r.phone AS receiver_phone, 
    r.address AS receiver_address
FROM 
    parcels p
JOIN 
    customers s ON p.sender_id = s.customer_id
JOIN 
    customers r ON p.receiver_id = r.customer_id
WHERE 
    p.parcel_id = ?";

$stmt = $conn->prepare($query);
$stmt->execute([$parcel_id]);
$parcel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parcel) {
    echo "Parcel not found.";
    exit;
}

function generateReportHTML($parcel)
{
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Parcel Report</title>
        <style>
           
            .containers {
                max-width: 800px;
                margin: 20px auto;
                background: #fff;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            h1 {
                text-align: center;
                margin-bottom: 20px;
            }

            .section {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .section div {
                width: 48%;
            }

            .section h2 {
                margin-top: 0;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }

            .details {
                margin-bottom: 20px;
            }

            .details p {
                margin: 5px 0;
            }

            .parcel-details {
                border-top: 2px solid #333;
                padding-top: 20px;
            }
        </style>
    </head>

    <body>
        <div class="containers" id="report">
            <h1>Parcel Report</h1>
            <div class="section">
                <div>
                    <h2>Sender Details</h2>
                    <div class="details">
                        <p><strong>Name:</strong>
                            <?php echo htmlspecialchars($parcel['sender_first_name'] . ' ' . $parcel['sender_last_name']); ?>
                        </p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($parcel['sender_email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($parcel['sender_phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($parcel['sender_address']); ?></p>
                    </div>
                </div>
                <div>
                    <h2>Receiver Details</h2>
                    <div class="details">
                        <p><strong>Name:</strong>
                            <?php echo htmlspecialchars($parcel['receiver_first_name'] . ' ' . $parcel['receiver_last_name']); ?>
                        </p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($parcel['receiver_email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($parcel['receiver_phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($parcel['receiver_address']); ?></p>
                    </div>
                </div>
            </div>
            <div class="parcel-details">
                <h2>Parcel Details</h2>
                <div class="details">
                    <p><strong>Parcel ID:</strong> <?php echo htmlspecialchars($parcel['parcel_id']); ?></p>
                    <p><strong>Weight:</strong> <?php echo htmlspecialchars($parcel['weight']); ?> kg</p>
                    <p><strong>Dimensions:</strong> <?php echo htmlspecialchars($parcel['dimensions']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($parcel['status']); ?></p>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
    return ob_get_clean();
}

if (isset($_GET['format']) && $_GET['format'] == 'pdf') {
    $html = generateReportHTML($parcel);

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('COURIER MANAGEMENT SYSTEM');
    $pdf->SetTitle('Parcel Report');
    $pdf->SetSubject('Parcel Report');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once (dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // set font
    $pdf->SetFont('dejavusans', '', 10);

    // add a page
    $pdf->AddPage();

    // output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // reset pointer to the last page
    $pdf->lastPage();

    //Close and output PDF document
    $pdf->Output('parcel_reports.pdf', 'D');
    exit;
}

$htmlReport = generateReportHTML($parcel);
include "../includes/header.php"
    ?>


<style>
    h1 {
        text-align: center;
        margin-bottom: 20px;
    }

    .section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .section div {
        width: 48%;
    }

    .section h2 {
        margin-top: 0;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }

    .details {
        margin-bottom: 20px;
    }

    .details p {
        margin: 5px 0;
    }

    .parcel-details {
        border-top: 2px solid #333;
        padding-top: 20px;
    }

    .download-buttons {
        text-align: center;
        margin-bottom: 20px;
    }

    .download-buttons select {
        padding: 10px;
        margin-right: 10px;
    }

    .download-buttons button {
        padding: 10px 20px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .download-buttons button:hover {
        background: #0056b3;
    }
</style>
<main class="mt-32 shadow-2xl w-max p-3 mx-auto bg-gray-50">
    <h1 class="text-2xl font-semibold text-gray-500">Parcel Report</h1>
    <div class="download-buttons">
        <select id="formatSelect">
            <option value="png">PNG</option>
            <option value="jpeg">JPEG</option>
            <option value="pdf">PDF</option>
        </select>
        <button onclick="downloadReport()">Download</button>
    </div>
    <?php echo $htmlReport; ?>
</main>
<script>
    function downloadReport() {
        const format = document.getElementById('formatSelect').value;
        if (format === 'pdf') {
            window.location.href = `reports.php?parcel_id=<?php echo htmlspecialchars($parcel_id); ?>&format=pdf`;
        } else {
            downloadImage(format);
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.5/dist/html2canvas.min.js"></script>
<script>
    function downloadImage(format) {
        const element = document.querySelector('#report');
        html2canvas(element).then(canvas => {
            let link = document.createElement('a');
            document.body.appendChild(link);
            link.download = `parcel_report_<?php echo htmlspecialchars($parcel['parcel_id']); ?>.${format}`;
            link.href = canvas.toDataURL(`image/${format}`);
            link.target = '_blank';
            link.click();
        });
    }
</script>
<?php include "../includes/script.php";
include "../includes/footer.php"; ?>