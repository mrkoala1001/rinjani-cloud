<div class="voucher-card" style="width:260px; border-radius:12px; border:2px solid #cbd5e1; overflow:hidden; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align:left; padding:0; background:#fff; position:relative; box-shadow:0 4px 6px rgba(0,0,0,0.05); margin-bottom: 10px;">
    <!-- Header -->
    <div style="background:#0f172a; color:white; padding:12px 10px; text-align:center;">
        <div style="font-size:16px; font-weight:900; letter-spacing:1px; text-transform:uppercase; text-shadow:1px 1px 2px rgba(0,0,0,0.5);">{{hotspotname}}</div>
    </div>
    
    <!-- Content -->
    <div style="padding:15px; display:flex; gap:12px; align-items:center;">
        {{qrcode}}
        <div style="flex:1;">
            <div style="font-size:9px; color:#64748b; text-transform:uppercase; font-weight:bold; letter-spacing:0.5px;">Kode / Username</div>
            <div style="font-size:18px; font-weight:900; color:#0f172a; letter-spacing:1px; line-height:1.2;">{{username}}</div>
            
            <div style="font-size:9px; color:#64748b; margin-top:6px; text-transform:uppercase; font-weight:bold; letter-spacing:0.5px;">Password</div>
            <div style="font-size:14px; font-weight:bold; color:#334155; line-height:1.2;">{{password}}</div>
        </div>
    </div>
    
    <!-- Details Box -->
    <div style="padding:10px 15px; background:#f8fafc; border-top:1px dashed #cbd5e1; border-bottom:1px dashed #cbd5e1; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div style="font-size:13px; font-weight:900; color:#ef4444;">{{selling_price}}</div>
            <div style="font-size:10px; font-weight:bold; color:#334155; margin-top:2px;">{{profile}}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:10px; color:#475569;"><span style="font-weight:bold;">Aktif:</span> {{validity}}</div>
            <div style="font-size:10px; color:#475569; margin-top:2px;"><span style="font-weight:bold;">Waktu:</span> {{timelimit}}</div>
        </div>
    </div>
    
    <!-- Footer -->
    <div style="padding:12px 15px; text-align:center; background:#fff;">
        <div style="font-size:11px; color:#1e293b; font-weight:bold; margin-bottom:5px;">
            Login Link: <span style="color:#2563eb;">{{login_link}}</span>
        </div>
        <div style="font-size:9px; color:#94a3b8; font-weight:600;">
            Dicetak/Reseller: <span style="color:#64748b;">{{reseller}}</span><br>
            Komplain/CS: <span style="color:#64748b;">{{wa_number}}</span>
        </div>
    </div>
</div>