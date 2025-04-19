<?php
// Firebase database URLs
$passagesUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/passages.json";
$usersUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/users.json";

// Function to fetch JSON data from Firebase
function getJsonData($url) {
    $jsonData = file_get_contents($url);
    return $jsonData ? json_decode($jsonData, true) : [];
}

// Fetch data from Firebase
$passages = getJsonData($passagesUrl);
$users = getJsonData($usersUrl);

// Get filters from the AJAX request
$filterUser = $_POST['filter_user'] ?? '0'; // Default: no user filter
$filterDate = $_POST['filter_date'] ?? '';  // Default: no date filter
$filterTime = $_POST['filter_time'] ?? '';  // Default: no time filter

// Initialize an array for all passages
$allPassages = [];

// Process the passages to collect and associate them with users
foreach ($passages as $deviceId => $userPassages) {
    foreach ($userPassages as $key => $passage) {
        // Ensure the passage has a valid UID and exists in the users list
        if (isset($passage['uid']) && isset($users[$passage['uid']])) {
            $timestamp = $passage['time'];
            $date = date('Y-m-d', $timestamp);
            $time = date('H:i:s', $timestamp);

            // Apply filters
            if (($filterUser === '0' || $filterUser === $passage['uid']) && // Filter by user
                ($filterDate === '' || $filterDate === $date) &&            // Filter by date
                ($filterTime === '' || $filterTime === $time)) {           // Filter by time
                $allPassages[] = [
                    'uid' => $passage['uid'],
                    'time' => $timestamp,
                    'device_id' => $passage['device_id']
                ];
            }
        }
    }
}

// Sort the passages by time (ascending order)
usort($allPassages, function ($a, $b) {
    return $a['time'] - $b['time'];
});

// Initialize arrays to track processed logs and user states
$uidPassages = []; // Tracks state of each UID
$result = []; // Final result set

// Iterate through the sorted passages to alternate between time_in and time_out
foreach ($allPassages as $passage) {
    $uid = $passage['uid'];

    // Initialize tracking for this UID if not already set
    if (!isset($uidPassages[$uid])) {
        $uidPassages[$uid] = ['count' => 0];
    }

    $uidPassages[$uid]['count']++;

    // Create a new log entry for the UID
    $entry = [
        'uid' => $uid,
        'name' => $users[$uid]['name'],
        'date' => date('Y-m-d', $passage['time']),
        'time_in' => '',
        'time_out' => ''
    ];

    // Assign time_in or time_out based on the passage count for this UID
    if ($uidPassages[$uid]['count'] % 2 == 1) {
        // Odd count: assign as time_in
        $entry['time_in'] = date('H:i:s', $passage['time']);
    } else {
        // Even count: assign as time_out
        $entry['time_out'] = date('H:i:s', $passage['time']);

        // Complete the log and add to the result
        $lastEntry = array_pop($result); // Get the last entry
        $lastEntry['time_out'] = $entry['time_out']; // Update time_out
        $result[] = $lastEntry; // Re-add updated entry
        continue;
    }

    // Add the entry to the result
    $result[] = $entry;
}

// Output the logs in an HTML table row format
if (empty($result)) {
    echo "<tr><td colspan='5'>No logs found for the selected filters.</td></tr>";
} else {
    foreach ($result as $row) {
        echo "<tr>
            <td>" . htmlspecialchars($row['uid']) . "</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['date']) . "</td>
            <td>" . htmlspecialchars($row['time_in']) . "</td>
            <td>" . htmlspecialchars($row['time_out']) . "</td>
        </tr>";
    }
}
?>