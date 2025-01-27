function updateStatus(userId, isDeleted) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_status.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Handle the response from the server
      console.log(xhr.responseText);
      var response = JSON.parse(xhr.responseText);
      return response;
    }
  };

  // Send the request with the status and user ID
  xhr.send(
    "status=" +
      encodeURIComponent(isDeleted) +
      "&user_id=" +
      encodeURIComponent(userId)
  );
}


function deleteUser(userId, status) {
   console.log(userId, status);
  if (status && confirm("Are You Sure You Want To Delete This User?")) {
    res = updateStatus(userId, status);
  }
  if (!status && confirm("Are You Sure You Want To Restore This User?")) {
    res = updateStatus(userId, status);
  }
}
