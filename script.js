function searchFlights() {
    // Retrieve form values
    var from = encodeURIComponent(document.getElementById('from').value);
    var to = encodeURIComponent(document.getElementById('to').value);
    var fromDateRaw = document.getElementById('fromDate').value;
    var toDateRaw = document.getElementById('toDate').value;
  
    // Log form values to the console
    console.log("From:", from);
    console.log("To:", to);
    console.log("From Date (Raw):", fromDateRaw);
    console.log("To Date (Raw):", toDateRaw);
  
    // Format dates as YYYY-MM-DD
    var fromDate = formatDate(fromDateRaw);
    var toDate = formatDate(toDateRaw);
  
    // Log formatted dates to the console
    console.log("Formatted From Date:", fromDate);
    console.log("Formatted To Date:", toDate);
  
    // Create an AJAX request
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
        // Log the response from the server to the console
        console.log("Server Response:", xhr.status, xhr.responseText);
  
        if (xhr.status == 200) {
          // Redirect to the search results page
          window.location.href = 'results.php';
        }
      }
    };
  
    // Prepare data to send
    var params = 'from=' + from + '&to=' + to + '&fromDate=' + fromDate + '&toDate=' + toDate;
  
    // Log data to be sent to the console
    console.log("Data to be Sent:", params);
  
    // Open and send the request
    xhr.open('POST', 'results.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(params);
  }
  
  // Function to format date as YYYY-MM-DD
  function formatDate(rawDate) {
    var date = new Date(rawDate);
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day = ('0' + date.getDate()).slice(-2);
    return year + '-' + month + '-' + day;
  }
  