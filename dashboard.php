<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expense Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #8e2de2, #4a00e0);
            margin: 0;
            padding: 0;
            color: #fff;
        }

        /* ====== Navbar ====== */
        .navbar {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* ====== Dashboard Section ====== */
        .dashboard {
            max-width: 1000px;
            margin: 60px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }

        .dashboard h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
        }

        .cards {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 25px;
            border-radius: 12px;
            width: 250px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            background-color: rgba(255, 255, 255, 0.25);
        }

        .card h3 {
            margin-bottom: 15px;
            font-size: 22px;
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #fff;
            color: #4a00e0;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }

        .card a:hover {
            background-color: #c9a6ff;
            color: #fff;
        }

        /* ====== Currency Converter Widget ====== */
        .currency-converter {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 25px;
            width: 420px;
            margin: 50px auto 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .currency-converter h2 {
            margin-bottom: 20px;
            color: #fff;
        }

        .converter-box {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .converter-box input,
        .converter-box select {
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            color: #333;
        }

        .converter-box button {
            background: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            color: #4a00e0;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .converter-box button:hover {
            background: #c9a6ff;
            color: #fff;
        }

        #result {
            margin-top: 20px;
            font-size: 18px;
            color: #fff;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <!-- ====== Navbar ====== -->
    <div class="navbar">
        <h1>BudgetBee</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">My Profile</a>
            <a href="change_password.php">Change Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- ====== Dashboard Section ====== -->
    <div class="dashboard">
        <h2>Welcome</h2>
        <div class="cards">
            <div class="card">
                <h3>Add Expense</h3>
                <a href="add_expense.php">Go</a>
            </div>
            <div class="card">
                <h3>View Expenses</h3>
                <a href="view_expenses.php">Go</a>
            </div>
            <div class="card">
                <h3>Add Category</h3>
                <a href="add_category.php">Go</a>
            </div>
            <div class="card">
                <h3>Set Budget</h3>
                <a href="set_budget.php">Go</a>
            </div>
            <div class="card">
                <h3>Budget vs Expense</h3>
                <a href="budget_expense_summary.php">Go</a>
            </div>
        </div>
    </div>

    <!-- ====== Currency Converter Widget ====== -->
    <div class="currency-converter">
        <h2>Currency Converter</h2>
        <div class="converter-box">
            <input type="number" id="amount" placeholder="Enter amount" />
            <select id="from-currency">
                <option value="BDT">BDT - Taka</option>
                <option value="USD">USD - Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="INR">INR - Rupee</option>
            </select>
            <span>to</span>
            <select id="to-currency">
                <option value="USD">USD - Dollar</option>
                <option value="BDT">BDT - Taka</option>
                <option value="EUR">EUR - Euro</option>
                <option value="INR">INR - Rupee</option>
            </select>
            <button onclick="convertCurrency()">Convert</button>
        </div>
        <p id="result"></p>
    </div>

    <script>
        // ==== Currency Conversion Rates (Static) ====
        const rates = {
            BDT: { USD: 0.009, EUR: 0.008, INR: 0.75, BDT: 1 },
            USD: { BDT: 120, EUR: 0.9, INR: 83, USD: 1 },
            EUR: { BDT: 133, USD: 1.11, INR: 92, EUR: 1 },
            INR: { BDT: 1.2, USD: 0.012, EUR: 0.011, INR: 1 }
        };

        // ==== Convert Function ====
        function convertCurrency() {
            const amount = parseFloat(document.getElementById("amount").value);
            const from = document.getElementById("from-currency").value;
            const to = document.getElementById("to-currency").value;
            const resultBox = document.getElementById("result");

            if (isNaN(amount) || amount <= 0) {
                resultBox.innerText = "Please enter a valid amount!";
                return;
            }

            const rate = rates[from][to];
            const converted = (amount * rate).toFixed(2);
            resultBox.innerText = `${amount} ${from} = ${converted} ${to}`;
        }
    </script>
</body>
</html>
