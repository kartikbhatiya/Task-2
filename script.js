function updateStatus(userId, isDeleted) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_status.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Handle the response from the server
      console.log(xhr.responseText);
      var response = JSON.parse(xhr.responseText);
      if (response.success) {
        alert('Status updated successfully');
      } else {
        alert('Failed to update status: ' + response.message);
      }
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
  let check = document.getElementById("status-" + userId);
  console.log("Delete User: " + userId);
  console.log(check.checked);
  if (status) {
    if (confirm("Are You Sure You Want To Delete This User?")) {
      updateStatus(userId, status);
    } else {
      console.log("User Delete Cancelled");
      check.checked = false;
    }
  } else {
    if (confirm("Are You Sure You Want To Restore This User?")) {
      updateStatus(userId, status);
    } else {
      console.log("User Restore Cancelled");
      check.checked = true;
    }
  }
}