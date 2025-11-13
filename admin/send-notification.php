<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Send Notification - Admin Panel</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      margin: 0; padding: 40px;
      display: flex; justify-content: center; align-items: center; min-height: 100vh;
    }
    form {
      background: #fff;
      padding: 30px 25px;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      width: 380px;
      box-sizing: border-box;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
      font-weight: 600;
    }
    label {
      font-weight: 600;
      color: #555;
      display: block;
      margin-bottom: 6px;
      margin-top: 15px;
    }
    input[type="text"],
    textarea,
    select {
      width: 100%;
      padding: 10px 12px;
      font-size: 15px;
      border: 1.8px solid #d1d5db;
      border-radius: 6px;
      outline-offset: 2px;
      outline-color: transparent;
      transition: outline-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
      resize: vertical;
      box-sizing: border-box;
    }
    input[type="text"]:focus,
    textarea:focus,
    select:focus {
      border-color: #2563eb;
      outline-color: #2563eb;
    }
    button {
      margin-top: 25px;
      width: 100%;
      background-color: #2563eb;
      color: white;
      font-weight: 700;
      font-size: 16px;
      border: none;
      padding: 12px;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #1d4ed8;
    }
  </style>
</head>
<body>
  <form id="notifForm">
    <h2>Send Notification</h2>

    <label for="mobile">Mobile Number (leave empty for all users)</label>
    <input type="text" id="mobile" name="mobile" placeholder="Enter mobile number or leave empty for broadcast" />

    <label for="title">Title</label>
    <input type="text" id="title" name="title" required placeholder="Enter notification title" />

    <label for="message">Message</label>
    <textarea id="message" name="message" rows="3" required placeholder="Enter notification message"></textarea>

    <label for="type">Type</label>
    <select id="type" name="type">
      <option value="system">System</option>
      <option value="promo">Promotion</option>
      <option value="order">Order</option>
    </select>

    <button type="submit">Send Notification</button>
  </form>

  <script>
    document.getElementById("notifForm").addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch("http://localhost/mealorderapp/mealorderapp-backend/api/send-notification.php", {
        method: "POST",
        body: formData,
      })
      .then(response => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then(data => {
        alert(data.message);
        if (data.success) this.reset();
      })
      .catch(err => {
        alert("Error: " + err.message);
        console.error(err);
      });
    });
  </script>
</body>
</html>
