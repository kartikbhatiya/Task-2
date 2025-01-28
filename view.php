<?php

include './controller/customers.php';
include './controller/images.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    global $customer;
    if (isset($_GET['id'])) {
        $customer = fetchCustomer($_GET['id']);
        $images  = fetchAllImages($customer['id']);
    }
    else{
        $customer = null;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Information</title>
    <style>

        img {
            width: 200px;
            height: 200px;
        }

        .deleted {
            position: relative;
            opacity: 0.5;
        }
        .deleted::after {
            content: "Deleted";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 0, 0, 0.7);
            color: white;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php if (isset($customer)): ?>
        <h1>Customer Information</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($customer['age']); ?></p>
        <p><strong>Birthday:</strong> <?php echo htmlspecialchars($customer['birthday']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($customer['gender']); ?></p>
        <p><strong>Subscribe:</strong> <?php echo htmlspecialchars(implode(', ', $customer['subscribe'])); ?></p>
        <p><strong>Country:</strong> <?php echo htmlspecialchars($customer['country']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($customer['message']); ?></p>
        <p><strong>Is Deleted:</strong> <?php if ($customer['isdeleted']) {
                                            echo "Yes";
                                        } else {
                                            echo "NO";
                                        }; ?></p>
        <p><strong>Created At:</strong> <?php echo htmlspecialchars($customer['created_at']); ?></p>
        <p><strong>Updated At:</strong> <?php echo htmlspecialchars($customer['updated_at']); ?></p>

        <h2>Images</h2>
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $image): ?>
                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Customer Image" class="<?php echo $image['isdeleted'] ? 'deleted' : ''; ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <p>No images available.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Provide ID of Customer.</p>
    <?php endif; ?>
</body>

</html>