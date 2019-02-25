<?php
include_once("includes/konekcija.php");
?>

<div>
  <ul id="kategorije">
    <?php
      $kategorije_stmt = $pdo->prepare("SELECT * FROM kategorije;");
      if($kategorije_stmt->execute()) {
        $kategorije = $kategorije->fetchAll();
        foreach($kategorije as $kategorija) {
          echo "<li data-category='{$kategorija->id}'>{$kategorija->name}</li>";
        }
      }
    ?>
  </ul>
</div>
<script>
  const lis = document.querySelectorAll("#kategorije li");
  const artikliContainer = document.querySelector("#artikli");

  lis.forEach(li => {
    li.addEventListener("click", async function() {
      const categId = parseInt(this.getAttribute("data-category"));
      const res = await fetch("/kategorije.api.php?id=" + categId);
      const json = await res.json();

      // redner item card je funckija koja bi vratila html string

      artikliContainer.innerHTML = json.map(item => renderItemCard(item)).join("")
    })
  });
</script>
