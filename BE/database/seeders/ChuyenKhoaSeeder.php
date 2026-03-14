<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuyenKhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuyen_khoas')->truncate();

        DB::table('chuyen_khoas')->insert([
            [
                'ten_chuyen_khoa' => 'Nội hô hấp',
                'mo_ta' => 'Chuyên khoa nội hô hấp tập trung chẩn đoán và điều trị các bệnh lý như viêm phổi, hen phế quản, bệnh phổi tắc nghẽn mãn tính (COPD), viêm phế quản và các rối loạn hô hấp khác. Bác sĩ sử dụng kỹ thuật nội soi phế quản, đo chức năng hô hấp và chẩn đoán hình ảnh để hỗ trợ điều trị hiệu quả.',
                'id_chuyen_nganh' => 1,
            ],
            [
                'ten_chuyen_khoa' => 'Ngoại thần kinh',
                'mo_ta' => 'Chuyên khoa này phụ trách phẫu thuật các bệnh lý liên quan đến hệ thần kinh trung ương và ngoại biên, bao gồm u não, chấn thương sọ não, thoát vị đĩa đệm và dị tật cột sống. Các bác sĩ thường thực hiện phẫu thuật vi phẫu, nội soi và phẫu thuật xâm lấn tối thiểu nhằm giảm thiểu rủi ro và phục hồi nhanh chóng cho bệnh nhân.',
                'id_chuyen_nganh' => 2,
            ],
            [
                'ten_chuyen_khoa' => 'Da liễu thẩm mỹ',
                'mo_ta' => 'Chuyên sâu điều trị các vấn đề về da như mụn trứng cá, nám, tàn nhang, sẹo rỗ và lão hóa da. Áp dụng công nghệ cao như laser CO2, lăn kim, PRP và peel hóa học để cải thiện thẩm mỹ và sức khỏe làn da. Ngoài ra, còn thực hiện tư vấn chăm sóc da phù hợp với từng loại da và lứa tuổi.',
                'id_chuyen_nganh' => 3,
            ],
            [
                'ten_chuyen_khoa' => 'Tai Mũi Họng trẻ em',
                'mo_ta' => 'Tập trung khám và điều trị các bệnh tai mũi họng thường gặp ở trẻ nhỏ như viêm amidan, viêm tai giữa, viêm VA, polyp mũi. Bác sĩ sử dụng các thiết bị chuyên dụng dành cho trẻ để đảm bảo an toàn, hiệu quả và tạo cảm giác thoải mái cho trẻ trong suốt quá trình khám chữa bệnh.',
                'id_chuyen_nganh' => 4,
            ],
            [
                'ten_chuyen_khoa' => 'Sản khoa',
                'mo_ta' => 'Chuyên cung cấp dịch vụ chăm sóc tiền sản, quản lý thai kỳ, tư vấn sinh nở và xử lý các biến chứng thai kỳ như tiền sản giật, sinh non, và thai ngược. Bác sĩ sản khoa cũng hỗ trợ sinh thường và sinh mổ, đồng thời theo dõi hồi phục hậu sản nhằm đảm bảo sức khỏe tối ưu cho mẹ và bé.',
                'id_chuyen_nganh' => 5,
            ],
            [
                'ten_chuyen_khoa' => 'Phụ khoa',
                'mo_ta' => 'Chuyên điều trị các bệnh lý như viêm nhiễm phụ khoa, rối loạn kinh nguyệt, u xơ tử cung, lạc nội mạc tử cung và ung thư phụ khoa. Chuyên khoa này còn thực hiện khám tầm soát ung thư cổ tử cung, xét nghiệm Pap và điều trị vô sinh nữ. Bác sĩ áp dụng các kỹ thuật hiện đại để mang lại hiệu quả cao.',
                'id_chuyen_nganh' => 5,
            ],
            [
                'ten_chuyen_khoa' => 'Nhi hô hấp - tiêu hóa',
                'mo_ta' => 'Phụ trách chăm sóc và điều trị các bệnh lý về hô hấp, tiêu hóa và dinh dưỡng ở trẻ như viêm phổi, tiêu chảy cấp, táo bón kéo dài, suy dinh dưỡng. Bác sĩ nhi chuyên khoa hô hấp - tiêu hóa phối hợp chặt chẽ với gia đình để theo dõi và điều trị toàn diện cho trẻ một cách hiệu quả và nhẹ nhàng.',
                'id_chuyen_nganh' => 6,
            ],
            [
                'ten_chuyen_khoa' => 'Nội tiết người lớn',
                'mo_ta' => 'Chẩn đoán và điều trị các rối loạn nội tiết như tiểu đường, cường giáp, suy giáp, rối loạn mỡ máu và bệnh lý tuyến thượng thận. Bác sĩ nội tiết sử dụng phương pháp điều trị cá nhân hóa, hướng dẫn chế độ ăn uống và lối sống phù hợp nhằm kiểm soát bệnh hiệu quả và lâu dài.',
                'id_chuyen_nganh' => 7,
            ],
            [
                'ten_chuyen_khoa' => 'Tim mạch can thiệp',
                'mo_ta' => 'Chuyên khoa thực hiện các kỹ thuật như chụp mạch vành, đặt stent động mạch, can thiệp mạch ngoại vi, và điều trị suy tim bằng phương pháp ít xâm lấn. Bác sĩ tim mạch can thiệp làm việc trong môi trường hiện đại với các thiết bị chẩn đoán hình ảnh tiên tiến để giảm thời gian điều trị và tăng tỷ lệ thành công.',
                'id_chuyen_nganh' => 8,
            ],
            [
                'ten_chuyen_khoa' => 'Phẫu thuật chỉnh hình cột sống',
                'mo_ta' => 'Tập trung phẫu thuật điều trị các bệnh lý cột sống như thoát vị đĩa đệm, hẹp ống sống, trượt đốt sống, cong vẹo cột sống và chấn thương cột sống. Bác sĩ chỉnh hình sử dụng kỹ thuật bắt vít, tạo hình đốt sống và phục hồi chức năng để cải thiện chất lượng sống cho bệnh nhân.',
                'id_chuyen_nganh' => 9,
            ],
            [
                'ten_chuyen_khoa' => 'Tư vấn tâm lý học đường',
                'mo_ta' => 'Cung cấp dịch vụ tham vấn và trị liệu tâm lý cho học sinh, sinh viên với các vấn đề như căng thẳng, lo âu, trầm cảm, áp lực học tập hoặc mâu thuẫn gia đình. Bác sĩ tâm lý sử dụng liệu pháp nhận thức – hành vi (CBT), trị liệu nhóm và kỹ năng sống để giúp học sinh ổn định tinh thần và phát triển tích cực.',
                'id_chuyen_nganh' => 10,
            ],
        ]);
    }
}
