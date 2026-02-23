<?php
// pages/voucher_templates.php

$default_template_code = <<<'EOT'
<?php
// Copy Paste ke template editor [Settings -> Template Editor].

if(substr($validity,-1) == "d"){
  $validity = "Aktif:".substr($validity,0,-1)."Hari";
}else if(substr($validity,-1) == "h"){
  $validity = "Aktif:".substr($validity,0,-1)."Jam";
}
if(substr($timelimit,-1) == "d" & strlen($timelimit) >3){
  $timelimit = "Durasi:".((substr($timelimit,0,-1)*7) +  substr($timelimit, 2,1))."Hari";
}else if(substr($timelimit,-1) == "d"){
  $timelimit = "Durasi:".substr($timelimit,0,-1)."Hari";
}else if(substr($timelimit,-1) == "h"){
  $timelimit = "Durasi:".substr($timelimit,0,-1)."Jam";
}else if(substr($timelimit,-1) == "w"){
  $timelimit = "Durasi:".(substr($timelimit,0,-1)*7)."Hari";
}

/* 
Sesuikan harga dan warna masing-masing.
warna bisa dilihat di https://material.io/guidelines/style/color.html#color-color-palette
variable $color
background-color:<?php echo $color;?>; -webkit-print-color-adjust: exact;
ditambahkan ke style di tag html yang ingin dikasi warna.
*/

if($getsprice == "1000"){ $color = "#2196F3";} // jika harga == "1000" maka warna = "#2196F3"
elseif($getsprice == "3000"){ $color = "#009688";}
elseif($getsprice == "5000"){ $color = "#FF9800";} // ini yang dicopy untuk menambah warna berdarsarkan harga, kemudian paste di atas baris // else color

// else color
else{ $color = "#FFFFFF";}
?>

<style type="text/css">
.rotate {
  vertical-align: bottom;
  text-align: center;
}
.rotate span {
  -ms-writing-mode: tb-rl;
  -webkit-writing-mode: vertical-rl;
  writing-mode: vertical-rl;
  transform: rotate(180deg);
  white-space: nowrap;
}
.qrcode{
		height:60px;
		width:60px;
}
</style>

<table class="voucher" style="width: 230px;">
  <tbody>
    <tr>
      <td class="rotate" style="font-weight: bold; border-right: 1px solid black; background-color:<?php echo $color;?>; -webkit-print-color-adjust: exact;" rowspan="4"><span><?= $price; ?></span></td>
      <td style="font-weight: bold" colspan="2"><?= $hotspotname; ?> </td>
      <?php if ($qr == "yes") { ?>
      <td style="" rowspan="3"><?= $qrcode ?></td>
      <?php 
    } else { ?>
      <td style="" rowspan="3"><img style="width: 60px; height: 60px;" src="<?= $logo ?>" alt="logo"></td>  
      <?php 
    } ?>
    </tr>
    <tr>
      <?php if ($usermode == "vc") { ?>  
      <td style="width: 100%; font-weight: bold; font-size: 20px; text-align: center;"><?= $username; ?></td>
      <?php 
    } elseif ($usermode == "up") { ?>
      <td style="width: 100%; font-weight: bold; font-size: 15px; text-align: center;"><?= "User: " . $username . "<br>Pass: " . $password; ?></td>
      <?php 
    } ?>  
    </tr>
    <tr>
      <td style="font-size: 10px;"><?= $validity; ?> <?= $timelimit; ?> <?= $datalimit; ?></td>
    </tr>
    <tr>
      <td colspan="3" style="font-size: 10px;">Login: http://<?= $dnsname; ?> <span id="num"> <?= " [$num]"; ?></span></td>
    </tr>
  </tbody>
</table>
EOT;
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Template Manager</h2>
        <button onclick="document.getElementById('addTemplateModal').classList.remove('hidden')" class="bg-primary hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
            <i class="fas fa-plus mr-2"></i>Tambah Template
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php if ($templates->num_rows > 0): ?>
            <?php while($tpl = $templates->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-lg transition">
                <div class="bg-gray-50 px-4 py-2 border-b flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Voucher Template</span>
                    <a href="index.php?page=voucher_templates&delete=<?php echo $tpl['id']; ?>" class="text-red-400 hover:text-red-600 transition" onclick="return confirm('Hapus template?')">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </a>
                </div>
                <div class="p-4 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-file-code text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-700 truncate"><?php echo htmlspecialchars($tpl['name']); ?></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Ready to use for printing</p>
                </div>
                <div class="p-2 border-t bg-gray-50 text-center">
                    <button class="text-[10px] font-bold text-secondary hover:underline uppercase">Edit Template</button>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full bg-white p-20 text-center border-2 border-dashed border-gray-300 rounded-lg">
                <i class="fas fa-clone fa-3x text-gray-200 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400">Belum ada template.</h3>
                <p class="text-gray-400 text-sm mt-2">Silahkan tambah template baru untuk mencetak voucher.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Template Modal -->
<div id="addTemplateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-2xl rounded-lg bg-white">
        <div class="mt-2">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Tambah Template Baru</h3>
                <button onclick="document.getElementById('addTemplateModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Template</label>
                    <input type="text" name="name" required placeholder="e.g. Thermal 58mm" class="w-full border rounded px-3 py-2 text-sm focus:ring-primary focus:outline-none">
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Template Code (PHP/HTML + CSS in &lt;style&gt;)</label>
                        <p class="text-[10px] text-gray-400 mb-2">Paste your full template code here. CSS inside &lt;style&gt; tags will be automatically extracted.</p>
                        <textarea name="html_content" rows="20" class="w-full border rounded px-3 py-2 text-xs font-mono bg-gray-900 text-green-400 focus:outline-none" placeholder="<?php echo htmlspecialchars($default_template_code); ?>"><?php echo htmlspecialchars($default_template_code); ?></textarea>
                    </div>
                </div>
                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" name="save_template" class="w-full bg-primary hover:bg-red-600 text-white font-bold py-2 rounded transition shadow-md">
                        <i class="fas fa-save mr-2"></i>Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
