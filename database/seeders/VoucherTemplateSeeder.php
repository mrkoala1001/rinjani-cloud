<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VoucherTemplate;

class VoucherTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Default Blue',
                'color' => '#2196F3',
            ],
            [
                'name' => 'Default Green',
                'color' => '#009688',
            ],
            [
                'name' => 'Default Orange',
                'color' => '#FF9800',
            ],
            [
                'name' => 'Default White',
                'color' => '#FFFFFF',
            ],
        ];

        foreach ($templates as $t) {
            $html = <<<HTML
	<style type="text/css">
	.rotate {
	  vertical-align: middle;
	  text-align: center;
      padding: 5px;
	}
	.rotate span {
	  -ms-writing-mode: tb-rl;
	  -webkit-writing-mode: vertical-rl;
	  writing-mode: vertical-rl;
	  transform: rotate(180deg);
	  white-space: nowrap;
      font-size: 14px;
	}
	.qrcode {
	  height: 70px;
	  width: 70px;
      display: block;
      margin: 0 auto;
	}
    .voucher {
      width: 250px;
      border: 1.5px solid #000;
      margin: 4px;
      display: inline-block;
      background: #fff;
      border-radius: 4px;
      overflow: hidden;
      font-family: 'Courier New', Courier, monospace;
    }
	</style>
	<table class="voucher">
	  <tbody>
	    <tr>
	      <td class="rotate" style="font-weight: bold; border-right: 1.5px solid #000; background-color:{$t['color']}; -webkit-print-color-adjust: exact;" rowspan="4">
	        <span>{{selling_price}}</span>
	      </td>
	      <td style="font-weight: bold; padding: 4px; font-size: 12px; border-bottom: 1px dashed #ccc" colspan="2">{{hotspotname}}</td>
	      <td style="padding: 5px; border-left: 1px dashed #ccc" rowspan="3" align="center" valign="middle">{{qrcode}}</td>
	    </tr>
	    <tr>
	      <td colspan="2" style="font-weight: bold; font-size: 18px; text-align: center; padding: 8px 4px;">
	        {{username}}<br>
	        <span style="font-size: 11px; font-weight: normal; color: #444;">{{password}}</span>
	      </td>
	    </tr>
	    <tr>
	      <td colspan="2" style="font-size: 10px; text-align: center; padding-bottom: 4px; border-top: 1px dashed #eee">{{validity}} / {{timelimit}}</td>
	    </tr>
	    <tr>
	      <td colspan="3" style="font-size: 9px; text-align: center; background: #f9f9f9; padding: 2px; border-top: 1px solid #000;">Login: http://{{hotspotname}}</td>
	    </tr>
	  </tbody>
	</table>
HTML;

            VoucherTemplate::updateOrCreate(
                ['name' => $t['name']],
                [
                    'user_id' => null, // System template, not owned by anyone
                    'is_system' => true,
                    'html_content' => $html,
                    'css_content' => '', // CSS is inline/internal
                ]
            );
        }
    }
}
