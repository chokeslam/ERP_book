# ERP_book

db name : bookERP

table : pdstock

<table name="pdstock">
    <tr>
        <td align="center">#</td>
        <td align="center">名稱</td>
        <td align="center">欄位型態</td>
        <td align="center">主索引</td>
        <td align="center">預設值</td>
        <td align="center">中文欄名</td>
        <td align="center">備註</td>
    </tr>
    <tr>
        <td align="center">0</td>
        <td align="center">PD_No</td>
        <td align="center">varcher(12)</td>
        <td align="center">Yes</td>
        <td align="center"></td>
        <td align="center">產品編號</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">1</td>
        <td align="center">ST_Qty</td>
        <td align="center">int(11)</td>
        <td align="center">No</td>
        <td align="center">0</td>
        <td align="center">庫存總量</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td align="center">ST_Place</td>
        <td align="center">varcher(20)</td>
        <td align="center">No</td>
        <td align="center"></td>
        <td align="center">存放位置</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">3</td>
        <td align="center">PR_Cdate</td>
        <td align="center">date</td>
        <td align="center">No</td>
        <td align="center"></td>
        <td align="center">盤點日期</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">4</td>
        <td align="center">UP_Time</td>
        <td align="center">timestamp</td>
        <td align="center">No</td>
        <td align="center">CURRENT_TIMESTAMP</td>
        <td align="center">時戳</td>
        <td align="center"></td>
    </tr>
</table>

table : iostock

<table name="iostock">
    <tr>
        <td align="center">#</td>
        <td align="center">名稱</td>
        <td align="center">欄位型態</td>
        <td align="center">主索引</td>
        <td align="center">預設值</td>
        <td align="center">中文欄名</td>
        <td align="center">備註</td>
    </tr>
    <tr>
        <td align="center">0</td>
        <td align="center">IO_Blno</td>
        <td align="center">varcher(12) not null</td>
        <td align="center">Yes</td>
        <td align="center"></td>
        <td align="center">異動單號</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">1</td>
        <td align="center">PD_No</td>
        <td align="center">varcher(12) not null</td>
        <td align="center">Yes</td>
        <td align="center">Pdstock , PD_No</td>
        <td align="center">產品編號</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td align="center">IN_Qty</td>
        <td align="center">DOUBLE DEFAULT0.0</td>
        <td align="center">No</td>
        <td align="center">0</td>
        <td align="center">入庫數量</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">3</td>
        <td align="center">QT_Qty</td>
        <td align="center">DOUBLE DEFAULT0.0</td>
        <td align="center">No</td>
        <td align="center"></td>
        <td align="center">出庫數量</td>
        <td align="center"></td>
    </tr>
    <tr>
        <td align="center">4</td>
        <td align="center">IO_Date</td>
        <td align="center">Date</td>
        <td align="center">No</td>
        <td align="center">0</td>
        <td align="center">異動日期</td>
        <td align="center"></td>
    </tr>   
    <tr>
        <td align="center">5</td>
        <td align="center">UP_Time</td>
        <td align="center">timestamp</td>
        <td align="center">No</td>
        <td align="center"></td>
        <td align="center">時戳</td>
        <td align="center"></td>
    </tr>
</table>

書籍條碼 共9碼
<table>
	<tr>
		<td>條碼</td>
		<td>12</td>
		<td>345</td>
		<td>678</td>
		<td>9</td>
	</tr>
	<tr>
		<td>意義</td>
		<td>年分</td>
		<td>課號</td>
		<td>老師編號</td>
		<td>該課程第幾本</td>
	</tr>
</table>
