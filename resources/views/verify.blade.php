<!DOCTYPE html>
<html>
<body>
    <h2>Szia {{ $user->name }} 👋</h2>

    <p>Kattints az email címed megerősítéséhez:</p>

    <a href="{{ $url }}" 
       style="padding:10px 20px; background:black; color:white; text-decoration:none;">
        Email megerősítése
    </a>

    <p>Ha nem te regisztráltál, hagyd figyelmen kívül.</p>
</body>
</html>