<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - KHANDELWAL ROADLINES</title>


    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/logo.png">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 40px;
            background: #fff;
            color: #000;
        }

        .invoice-box {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #003F72;
        }

        .company {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            color: #c72336;
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        table th {
            background-color: #f0f0f0;
        }

        .no-border td {
            border: none;
            padding: 4px 0;
        }

        .total-section td {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .note {
            font-style: italic;
            margin-top: 20px;
        }

        .signature {
            text-align: right;
            margin-top: 50px;
        }

        .bank-details {
            margin-top: 20px;
        }

        .company img {
            width: 60%;
            float: inline-end;
            margin-top: -29px;
        }

        .print-btn {
            padding: 12px 28px;
            background-color: #ca2639;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease;
            z-index: 1000;
            margin: auto;
            margin-top: 2%;
            margin-bottom: 1%;
            text-align: center;
            display: block;
        }

        .print-btn:hover {
            background-color: #a71f2e;
        }

        .te p {
            margin: 9px 0;
        }

        /* Hide Print Button on Print */
        /* Print Specific Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 12px;
                /* Reduce font size for better fit */
            }

            .invoice-box {
                max-width: 100%;
                margin: 0;
                padding: 15px;
                border: none;
                box-shadow: none;
                font-size: 12px;
            }

            .title {
                font-size: 16px;
                /* Slightly smaller title */
            }

            .company {
                font-size: 14px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table th,
            table td {
                padding: 6px;
                /* Reduce padding to fit more content */
                font-size: 12px;
            }

            /* Prevent page breaks within the table */
            table,
            tr,
            td {
                page-break-inside: avoid;
            }

            .no-border td {
                border: none;
                padding: 4px 0;
            }

            /* Ensure content doesn't overflow outside page */
            .print-btn {
                display: none;
                /* Hide print button on printed page */
            }

            .note,
            .signature,
            .bank-details {
                font-size: 12px;
                /* Smaller text to avoid overflow */
            }

            .flex-row {
                display: block;
                /* Stack the elements for print */
                margin-top: 5px;
            }

            .flex-row .te {
                margin-bottom: 10px;
            }

            /* Make sure page doesn't overflow */
            .invoice-box {
                page-break-before: always;
                page-break-after: always;
            }
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .flex-row .te {
            width: 48%;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Right align for te2 */
        .flex-row .te2 {
            text-align: right;
        }

        /* Ensure te2 content stays aligned during print */
        @media print {
            .flex-row {
                display: flex;
                justify-content: space-between;
            }

            .flex-row .te,
            .flex-row .te2 {
                width: 48%;
                font-size: 12px;
            }

            .flex-row .te2 {
                text-align: right;
            }

            .print-btn {
                display: none;
                /* Hide print button when printing */
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">

        <div class="flex-row">
            <div>
                <div class="title">TAX INVOICE</div>
                <div>ORIGINAL FOR RECIPIENT</div>
            </div>
            <div class="company">
                <img src="assets/img/logo.jpg" alt="">
            </div>
        </div>

        <div class="flex-row">
            <div class="te">
                <p><strong>GSTIN:</strong> 23AAAFK1234L1Z5</p>
                <p><strong>Head Office:</strong> Khandelwal RoadLines, Opp. Abhinav Talkies, Ujjain Road, Dewas - 455001
                </p>
                <p><strong>Mobile:</strong>9098733332, 9770533332</p>
                <p><strong>Offices :</strong>Mumbai: 9326145500, Indore: 9303188889</p>
                <p><strong>Email:</strong>krl@khandelwalroadlines.com</p>
            </div>
            <div class="te te2">
                <p><strong>Invoice No:</strong> 25-261</p>
                <p><strong>Invoice Date:</strong> 01 Apr 2025</p>
                <p><strong>Due Date:</strong> 05 Apr 2025</p>
            </div>
        </div>

        <table class="no-border">
            <tr>
                <td><strong>Customer Details:</strong> Quantum Shipping & Logistics</td>
                <td><strong>Billing Address:</strong> Flat No. 702, Wing G2, Nerul, Navi Mumbai</td>
            </tr>
            <tr>
                <td><strong>GSTIN:</strong> 27AAAFQ9613R1ZK</td>
                <td><strong>Place of Supply:</strong> Maharashtra</td>
            </tr>
            <tr>
                <td><strong>Container No:</strong> HLBU9085831</td>
                <td><strong>LR No.:</strong> LR24001</td>
            </tr>
            <tr>
                <td><strong>LR Date:</strong> 28/03/2025</td>
                <td><strong>Vehicle No:</strong> MP09HG1234</td>
            </tr>
            <tr>
                <td><strong>Delivery Location:</strong> ASHTE CFS</td>
                <td><strong>Mode of Transport:</strong> Road</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Description</th>
                    <th>Rate / Item</th>
                    <th>Qty</th>
                    <th>Taxable Value</th>
                    <th>Tax Amount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>TRANSPORTATION CHARGES <br><small>SAC: 996791</small></td>
                    <td>1,05,000.00</td>
                    <td>1</td>
                    <td>1,05,000.00</td>
                    <td>12,600.00 (12%)</td>
                    <td>1,17,600.00</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>EXTRA CHARGES<br><small>VEHICLE SENT TO ASHTE CFS</small></td>
                    <td>5,000.00</td>
                    <td>1</td>
                    <td>5,000.00</td>
                    <td>600.00 (12%)</td>
                    <td>5,600.00</td>
                </tr>
                <tr class="total-section">
                    <td colspan="4"></td>
                    <td>₹1,10,000.00</td>
                    <td>₹13,200.00</td>
                    <td>₹1,23,200.00</td>
                </tr>
            </tbody>
        </table>

        <p class="note"><strong>Amount in Words:</strong> One Lakh Twenty Three Thousand Two Hundred Rupees Only</p>

        <div class="bank-details">
            <p><strong>Bank Name:</strong> HDFC BANK</p>
            <p><strong>Account No:</strong> 50200012345678</p>
            <p><strong>IFSC Code:</strong> HDFC0001234</p>
            <p><strong>Branch:</strong> Indore Main Branch</p>
        </div>

        <div class="signature">
            <p>For KHANDELWAL ROADLINES</p>
            <br><br>
            <p>Authorized Signatory</p>
        </div>

        <p class="note">Note: Please issue TDS certificate under 194C with PAN: ABCDE1234F</p>

    </div>

    <!-- Print Button -->
    <button class="print-btn" onclick="window.print()">🖨️ Print Invoice </button>
</body>

</html>