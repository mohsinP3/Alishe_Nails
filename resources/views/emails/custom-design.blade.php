<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Custom design request</title></head>
<body style="font-family: Arial, sans-serif; padding:24px;">
    <h2>New custom design request</h2>
    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
    <p><strong>Preferred style:</strong> {{ $data['style'] }}</p>
    <p><strong>Budget:</strong> {{ $data['budget'] ?? 'Not specified' }}</p>
    <p><strong>Details:</strong></p>
    <p>{{ $data['details'] }}</p>
</body>
</html>
