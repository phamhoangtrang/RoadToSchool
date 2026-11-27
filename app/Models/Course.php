<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    const SELLER_ASC = 'seller|asc';
    const SELLER_DESC = 'seller|desc';
    const PRICE_ASC = 'promotion_price|asc';
    const PRICE_DESC = 'promotion_price|desc';
    const LEVEL_ASC = 'level|asc';
    const LEVEL_DESC = 'level|desc';
    const DURATION_ASC = 'duration|asc';
    const DURATION_DESC = 'duration|desc';

    public static $filter_options = [
        self::SELLER_ASC => 'Lượt mua tăng dần',
        self::SELLER_DESC => 'Lượt mua giảm dần',
        self::PRICE_ASC => 'Giá tiền tăng dần',
        self::PRICE_DESC => 'Giá tiền giảm dần',
        self::LEVEL_ASC => 'Cấp độ tăng dần',
        self::LEVEL_DESC => 'Cấp độ giảm dần',
        self::DURATION_ASC => 'Thời lượng tăng dần',
        self::DURATION_DESC => 'Thời lượng giảm dần',
    ];

    protected $table = 'courses';

    protected $fillable = [
        'title',
        'course_avatar',
        'course_avatar_2',
        'course_avatar_3',
        'description',
        'origin_price',
        'promotion_price',
        'lecture_numbers',
        'duration',
        'seller',
        'level',
        'course_rate',
        'is_accepted',
        'category_id',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * Get the teacher that teach the course.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function cart_items()
    {
        return $this->hasMany('App\Models\CartItem');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function lectures()
    {
        return $this->hasMany('App\Models\Lecture');
    }

    public function getAllCourse($params = array())
    {
        $builder = Course::where('is_accepted', 1);

        // Áp dụng sắp xếp theo bộ lọc
        if (isset($params['filter_option']) && !empty($params['filter_option']) && $params['filter_option'] !== '0') {
            $filterCondition = $params['filter_option'];
            $partOfVerticalBar = explode("|", $filterCondition, 2);
            if (count($partOfVerticalBar) === 2) {
                $fieldSearch = $partOfVerticalBar[0];
                $sortOption = strtolower($partOfVerticalBar[1]) === 'desc' ? 'desc' : 'asc';

                if ($fieldSearch === 'promotion_price' || $fieldSearch === 'price') {
                    // Sắp xếp theo giá bán thực tế hiển thị cho người dùng
                    $builder->orderByRaw('CASE WHEN promotion_price > 0 AND promotion_price < origin_price THEN promotion_price ELSE origin_price END ' . $sortOption);
                } elseif (in_array($fieldSearch, ['seller', 'level', 'duration', 'course_rate', 'created_at', 'updated_at'])) {
                    $builder->orderBy($fieldSearch, $sortOption);
                } else {
                    $builder->orderBy('updated_at', 'desc');
                }
            } else {
                $builder->orderBy('updated_at', 'desc');
            }
        } else {
            $builder->orderBy('updated_at', 'desc');
        }

        // Tie-breaker ổn định cho phân trang
        $builder->orderBy('id', 'desc');

        if (isset($params['course_rate']) && $params['course_rate'] && $params['course_rate'] !== '0') {
            $builder->where('course_rate', '>=', $params['course_rate']);
        }
        if (isset($params['keyword']) && trim($params['keyword']) !== '') {
            $builder->where('title', 'LIKE', '%' . trim($params['keyword']) . '%');
        }
        if (isset($params['sub_category_id']) && $params['sub_category_id']) {
            $categoryIdList = Category::where('id', $params['sub_category_id'])->pluck('id');
            $builder->whereIn('category_id', $categoryIdList);
        }
        if (isset($params['category_id']) && $params['category_id']) {
            $categoryIdList = Category::where('parent_id', $params['category_id'])->pluck('id');
            $builder->whereIn('category_id', $categoryIdList);
        }
        if (isset($params['user_id']) && $params['user_id']) {
            $builder->where('user_id', $params['user_id']);
        }

        return $builder->paginate(8)->withQueryString();
    }

    public function findCourse($id)
    {
        return Course::findOrFail($id);
    }

    public function findMostRelatedCourse($id)
    {
        $selectedCategoryId = Course::findOrFail($id)->category_id;

        return Course::where('is_accepted', 1)->where('category_id', $selectedCategoryId)->orderBy('seller', 'desc')->limit(5)->get();
    }

    public function findRelatedCourseCount($id)
    {
        $selectedCategoryId = Course::findOrFail($id)->category_id;

        return Course::where('is_accepted', 1)->where('category_id', $selectedCategoryId)->get()->count();
    }

    public function createNewCourse($request)
    {
        $data = $request->all();
        $uploadPath = public_path('/images/course_avatar/');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // 1. Ảnh bìa chính
        if ($request->hasFile('course_avatar') && $request->file('course_avatar')->isValid()) {
            $file = $request->file('course_avatar');
            $fileExtension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = time() . "_" . rand(0, 9999999) . "_" . md5(rand(0, 9999999)) . "." . $fileExtension;
            $file->move($uploadPath, $fileName);
            $data['course_avatar'] = 'public/images/course_avatar/' . $fileName;
        } else {
            // Mặc định ảnh đẹp nếu không tải lên hoặc file vượt giới hạn
            $data['course_avatar'] = 'public/images/course_avatar/web_development_1.jpeg';
        }

        // 2. Ảnh phụ Slide 2 (Tùy chọn)
        if ($request->hasFile('course_avatar_2') && $request->file('course_avatar_2')->isValid()) {
            $file = $request->file('course_avatar_2');
            $fileExtension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = time() . "_" . rand(0, 9999999) . "_" . md5(rand(0, 9999999)) . "." . $fileExtension;
            $file->move($uploadPath, $fileName);
            $data['course_avatar_2'] = 'public/images/course_avatar/' . $fileName;
        } else {
            $data['course_avatar_2'] = $data['course_avatar'];
        }

        // 3. Ảnh minh họa Slide 3 (Tùy chọn)
        if ($request->hasFile('course_avatar_3') && $request->file('course_avatar_3')->isValid()) {
            $file = $request->file('course_avatar_3');
            $fileExtension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = time() . "_" . rand(0, 9999999) . "_" . md5(rand(0, 9999999)) . "." . $fileExtension;
            $file->move($uploadPath, $fileName);
            $data['course_avatar_3'] = 'public/images/course_avatar/' . $fileName;
        } else {
            $data['course_avatar_3'] = $data['course_avatar'];
        }

        $data['description'] = $request->input('description') ?: '';
        $data['origin_price'] = $request->filled('origin_price') ? floatval($request->input('origin_price')) : 0;
        $data['promotion_price'] = ($request->filled('promotion_price') && floatval($request->input('promotion_price')) > 0)
            ? floatval($request->input('promotion_price'))
            : null;
        $data['lecture_numbers'] = 0;
        $data['duration'] = 0;
        $data['seller'] = 0;
        $data['course_rate'] = 0;
        $data['is_accepted'] = 0;
        $data['user_id'] = \Auth::user()->id;

        return Course::create($data);
    }
}
