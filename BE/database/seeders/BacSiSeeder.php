<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BacSiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bac_sis')->truncate();
        DB::table('bac_sis')->insert([
            [
                'ho_ten' => 'Nguyễn Văn K',
                'email' => 'a@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000001',
                'ngay_sinh' => '1980-01-01',
                'gioi_tinh' => 'Nam',
                'mo_ta' => 'Bác sĩ chuyên khoa tim mạch với hơn 15 năm kinh nghiệm trong chẩn đoán và điều trị các bệnh lý tim mạch phức tạp.
Ông từng công tác tại các bệnh viện tuyến trung ương và tham gia nhiều hội thảo y khoa quốc tế.
Với kiến thức sâu rộng và sự tận tâm, ông đã điều trị thành công cho hàng nghìn bệnh nhân.',
                'hinh_anh' => 'https://media.benhvienhathanh.vn/media/doi_ngu_bac_si/b%C3%A1c_s%E1%BB%B9_%C4%91o%C3%A0n_h%E1%BB%AFu_%C4%91%C3%B4ng.png',
                'chuc_danh' => 'Trưởng khoa',
                'so_nam_kinh_nghiem' => '15',
                'kinh_nghiem_lam_viec' => 'Bệnh viện Bạch Mai',
                'qua_trinh_dao_tao' => 'ĐH Y Hà Nội',
                'chung_chi' => 'Chứng chỉ chuyên sâu về tim mạch, can thiệp mạch vành, siêu âm tim nâng cao',
                'thanh_tuu' => 'Bác sĩ xuất sắc năm 2020',
                'thanh_vien_to_chuc' => 'Hội nội khoa Việt Nam',
                'tinh_trang' => 1,
                'id_phong_kham' => 1,
            ],
            [
                'ho_ten' => 'Trần Thị Q',
                'email' => 'b@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000002',
                'ngay_sinh' => '1985-05-10',
                'gioi_tinh' => 'Nữ',
                'mo_ta' => 'Bác sĩ da liễu chuyên điều trị các bệnh về da liễu mãn tính, dị ứng và thẩm mỹ da.
Chị từng giữ vị trí bác sĩ chính tại Bệnh viện Da liễu Trung Ương trong nhiều năm.
Ngoài công việc khám chữa bệnh, chị còn là giảng viên thỉnh giảng và tác giả nhiều công trình nghiên cứu.',
                'hinh_anh' => 'https://images2.thanhnien.vn/thumb_w/686/528068263637045248/2024/3/7/41498385661961282804899348165590311304931596n-17098051418122006775403-0-286-2048-1822-crop-1709805739243640175866.jpg',
                'chuc_danh' => 'Bác sĩ chính',
                'so_nam_kinh_nghiem' => '12',
                'kinh_nghiem_lam_viec' => 'Bệnh viện Da liễu Trung Ương',
                'qua_trinh_dao_tao' => 'ĐH Y Dược TP.HCM',
                'chung_chi' => 'Chứng chỉ điều trị laser, điều trị mụn và lão hóa da chuyên sâu',
                'thanh_tuu' => 'Tác giả công trình "Ứng dụng PRP trong điều trị sẹo rỗ"',
                'thanh_vien_to_chuc' => 'Hội Da liễu Việt Nam',
                'tinh_trang' => 0,
                'id_phong_kham' => 2,
            ],
            [
                'ho_ten' => 'Lê Văn P',
                'email' => 'c@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000003',
                'ngay_sinh' => '1975-07-12',
                'gioi_tinh' => 'Nam',
                'mo_ta' => 'Bác sĩ ngoại thần kinh với hơn 20 năm công tác tại các bệnh viện tuyến đầu.
Ông có kỹ năng phẫu thuật cao trong các ca chấn thương sọ não và u não phức tạp.
Luôn không ngừng cập nhật kiến thức mới và công nghệ y học tiên tiến.',
                'hinh_anh' => 'https://hthaostudio.com/wp-content/uploads/2022/03/Anh-bac-si-nam-7-min.jpg.webp',
                'chuc_danh' => 'Phó khoa',
                'so_nam_kinh_nghiem' => '20',
                'kinh_nghiem_lam_viec' => 'Bệnh viện Quân đội 108',
                'qua_trinh_dao_tao' => 'ĐH Y Huế',
                'chung_chi' => 'Chứng chỉ phẫu thuật thần kinh nâng cao và điều trị chấn thương sọ não',
                'thanh_tuu' => 'Giải thưởng Y học quốc gia 2019',
                'thanh_vien_to_chuc' => 'Hội Ngoại khoa Việt Nam',
                'tinh_trang' => 0,
                'id_phong_kham' => 3,
            ],
            [
                'ho_ten' => 'Phạm Thị M',
                'email' => 'd@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000004',
                'ngay_sinh' => '1990-09-15',
                'gioi_tinh' => 'Nữ',
                'mo_ta' => 'Chuyên khoa Nhi, đặc biệt trong việc điều trị các bệnh lý hô hấp, tiêu hóa và dinh dưỡng ở trẻ nhỏ.
Chị luôn tạo không gian thân thiện và gần gũi để trẻ em cảm thấy thoải mái trong quá trình khám chữa bệnh.
Ngoài ra, chị tích cực tham gia các chương trình chăm sóc sức khỏe cộng đồng.',
                'hinh_anh' => 'https://luxclinic.vn/wp-content/uploads/2024/07/bac-Phuong-1.jpg',
                'chuc_danh' => 'Bác sĩ',
                'so_nam_kinh_nghiem' => '8',
                'kinh_nghiem_lam_viec' => 'Nhi TW',
                'qua_trinh_dao_tao' => 'ĐH Y Thái Bình',
                'chung_chi' => 'Chứng chỉ Nhi khoa cơ bản và nâng cao, đào tạo tâm lý trẻ em',
                'thanh_tuu' => 'Chăm sóc trẻ em vùng sâu',
                'thanh_vien_to_chuc' => 'Hội Nhi Việt Nam',
                'tinh_trang' => 1,
                'id_phong_kham' => 2,
            ],
            [
                'ho_ten' => 'Đỗ Văn E',
                'email' => 'e@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000005',
                'ngay_sinh' => '1982-12-01',
                'gioi_tinh' => 'Nam',
                'mo_ta' => 'Bác sĩ chuyên về chỉnh hình và phục hồi chức năng sau chấn thương.
Anh từng trực tiếp tham gia các ca mổ phức tạp liên quan đến gãy xương và dị tật bẩm sinh.
Anh cũng tham gia đào tạo kỹ thuật nẹp xương hiện đại cho bác sĩ tuyến dưới.',
                'hinh_anh' => 'https://bvdkgiadinh.com/wp-content/uploads/2023/03/Anh-bac-si-Web_ThS.-BS.-DOAN-TRONG-NGHIA-.jpg',
                'chuc_danh' => 'Bác sĩ',
                'so_nam_kinh_nghiem' => '10',
                'kinh_nghiem_lam_viec' => 'BV Chỉnh hình',
                'qua_trinh_dao_tao' => 'ĐH Y Cần Thơ',
                'chung_chi' => 'Chứng chỉ phẫu thuật chỉnh hình và điều trị sau phẫu',
                'thanh_tuu' => 'Tham gia cứu trợ y tế sau thiên tai',
                'thanh_vien_to_chuc' => 'Hội Chỉnh hình VN',
                'tinh_trang' => 0,
                'id_phong_kham' => 1,
            ],
            [
                'ho_ten' => 'Lý Thị V',
                'email' => 'f@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000006',
                'ngay_sinh' => '1991-06-20',
                'gioi_tinh' => 'Nữ',
                'mo_ta' => 'Chuyên gia nội tiết, đặc biệt là các bệnh lý về tuyến giáp và đái tháo đường.
Chị có phương pháp tiếp cận toàn diện giúp bệnh nhân hiểu rõ bệnh của mình để điều trị hiệu quả.
Ngoài công việc khám bệnh, chị còn tham gia nghiên cứu các xu hướng điều trị mới.',
                'hinh_anh' => 'https://benhvienthanhvubaclieu.com/cms/static/site/sale_medicthanhvu/uploads/doctor/doctor.avatar.b9a6bdec56573449.3532202d204e475559454e20544849205448414e4820544855592e6a7067.jpg',
                'chuc_danh' => 'Thạc sĩ',
                'so_nam_kinh_nghiem' => '7',
                'kinh_nghiem_lam_viec' => 'BV Nội tiết',
                'qua_trinh_dao_tao' => 'ĐH Y Hải Phòng',
                'chung_chi' => 'Chứng chỉ điều trị nội tiết và chăm sóc bệnh nhân mãn tính',
                'thanh_tuu' => 'Thực hiện đề tài về rối loạn nội tiết nữ',
                'thanh_vien_to_chuc' => 'Hội Nội tiết học',
                'tinh_trang' => 1,
                'id_phong_kham' => 1,
            ],
            [
                'ho_ten' => 'Vũ Văn D',
                'email' => 'g@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000007',
                'ngay_sinh' => '1987-07-17',
                'gioi_tinh' => 'Nam',
                'mo_ta' => 'Bác sĩ chuyên khoa tiêu hóa với kinh nghiệm phong phú trong nội soi và điều trị viêm loét dạ dày.
Anh luôn đặt hiệu quả điều trị và sự hài lòng của bệnh nhân lên hàng đầu.
Anh tích cực ứng dụng công nghệ nội soi hiện đại trong chẩn đoán sớm ung thư tiêu hóa.',
                'hinh_anh' => 'https://www.fvhospital.com/wp-content/uploads/2018/03/dr-vo-trieu-dat-2020.jpg',
                'chuc_danh' => 'Bác sĩ nội soi',
                'so_nam_kinh_nghiem' => '13',
                'kinh_nghiem_lam_viec' => 'BV Việt Đức',
                'qua_trinh_dao_tao' => 'ĐH Y Huế',
                'chung_chi' => 'Chứng chỉ nội soi dạ dày, đại tràng và phát hiện polyp sớm',
                'thanh_tuu' => 'Ứng dụng AI vào nội soi',
                'thanh_vien_to_chuc' => 'Hội tiêu hóa',
                'tinh_trang' => 0,
                'id_phong_kham' => 3,
            ],
            [
                'ho_ten' => 'Trịnh Thị T',
                'email' => 'h@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000008',
                'ngay_sinh' => '1983-03-13',
                'gioi_tinh' => 'Nữ',
                'mo_ta' => 'Chuyên viên tâm lý học đường, có thế mạnh trong tư vấn học sinh và phụ huynh.
Chị đã tham gia hơn 500 ca tư vấn trực tiếp trong các trường học.
Chị còn điều hành một nhóm hỗ trợ tâm lý trực tuyến miễn phí.',
                'hinh_anh' => 'https://bvdkgiadinh.com/wp-content/uploads/2023/07/Anh-bac-si-Web.jpg',
                'chuc_danh' => 'Tiến sĩ',
                'so_nam_kinh_nghiem' => '9',
                'kinh_nghiem_lam_viec' => 'Viện Tâm lý VN',
                'qua_trinh_dao_tao' => 'ĐH KHXH&NV',
                'chung_chi' => 'Chứng chỉ trị liệu nhận thức – hành vi và can thiệp học đường',
                'thanh_tuu' => 'Tư vấn học sinh ở vùng khó khăn',
                'thanh_vien_to_chuc' => 'Hội tâm lý học Việt Nam',
                'tinh_trang' => 1,
                'id_phong_kham' => 2,
            ],
            [
                'ho_ten' => 'Phan Văn H',
                'email' => 'i@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000009',
                'ngay_sinh' => '1992-02-22',
                'gioi_tinh' => 'Nam',
                'mo_ta' => 'Bác sĩ trẻ chuyên về chấn thương chỉnh hình, đặc biệt là các chấn thương thể thao.
Anh có tư duy hiện đại và luôn cập nhật kỹ thuật mổ nội soi mới nhất.
Anh từng tham gia khóa tu nghiệp 6 tháng tại Hàn Quốc về chỉnh hình gối.',
                'hinh_anh' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTXM9s1FZTOukAeEeIjG_H6CctGxdfTsgjeArcFOk_Hmvf5M5-mroa1aUWyayC8Blns7o&usqp=CAU',
                'chuc_danh' => 'Bác sĩ',
                'so_nam_kinh_nghiem' => '5',
                'kinh_nghiem_lam_viec' => 'BV Tỉnh A',
                'qua_trinh_dao_tao' => 'ĐH Y Huế',
                'chung_chi' => 'Chứng chỉ mổ nội soi khớp gối, khớp vai',
                'thanh_tuu' => 'Giải thưởng bác sĩ trẻ 2022',
                'thanh_vien_to_chuc' => 'Hội CTCH VN',
                'tinh_trang' => 0,
                'id_phong_kham' => 2,
            ],
            [
                'ho_ten' => 'Lý Thị R',
                'email' => 'j@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0901000010',
                'ngay_sinh' => '1986-08-18',
                'gioi_tinh' => 'Nữ',
                'mo_ta' => 'Bác sĩ sản phụ khoa chuyên hỗ trợ sinh sản và điều trị vô sinh hiếm muộn.
Chị từng đảm nhiệm nhiều ca IVF và IUI thành công.
Chị cũng tích cực tham gia các khóa đào tạo quốc tế về công nghệ hỗ trợ sinh sản.',
                'hinh_anh' => 'https://tamanhhospital.vn/wp-content/uploads/2020/05/bs-pham-do-anh-thu-detail.png',
                'chuc_danh' => 'Tiến sĩ',
                'so_nam_kinh_nghiem' => '14',
                'kinh_nghiem_lam_viec' => 'Từ Dũ',
                'qua_trinh_dao_tao' => 'ĐH Y TPHCM',
                'chung_chi' => 'Chứng chỉ IVF, IUI, nội soi tử cung - buồng trứng',
                'thanh_tuu' => 'Hơn 100 ca hỗ trợ sinh sản thành công',
                'thanh_vien_to_chuc' => 'Hội sản phụ khoa Việt Nam',
                'tinh_trang' => 0,
                'id_phong_kham' => 3,
            ]
        ]);
    }
}
