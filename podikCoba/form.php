<!-- form.php -->
<form method="get" action="index.php">
    <label for="kodePLU">Masukkan Kode PLU:</label>
    <input type="text" name="kodePLU" id="kodePLU" value="<?= htmlspecialchars($_GET['kodePLU'] ?? '') ?>" maxlength="7" />
    <button type="submit">Cari</button>
</form>
