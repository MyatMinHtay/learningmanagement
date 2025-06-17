<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Blog;
use App\Models\BlogGenre;
use App\Models\Chapter;
use App\Models\Genre;
use App\Models\StarPackage;
use App\Models\SystemRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = SystemRole::factory()->create(['role' => 'user']);
        $admin = SystemRole::factory()->create(['role'=>'admin','permissions'=>'dashboard,webtoons,chapters,orders,genres']);
        $adminstrator = SystemRole::factory()->create(['role'=>'adminstrator','permissions'=>'all,dashboard,webtoons,chapters,orders,genres']);

        $mgmg = User::factory()->create(['role_id' => $user->id,'name'=>'mgmg','username'=>'mgmg','email'=>'mgmg@gmail.com','password'=>'$2y$10$UMcXX5XKLaqeQiIIjSccBuJXs7tW1d0xOptcFs/.fryBubakwouTy','userphoto'=>'./assets/img/profile.png','star'=>50]);
        $aungaung = User::factory()->create(['role_id' => $user->id,'name'=>'aungaung','username'=>'aungaung','email'=>'aungaung@gmail.com','password'=>'$2y$10$UMcXX5XKLaqeQiIIjSccBuJXs7tW1d0xOptcFs/.fryBubakwouTy','userphoto'=>'./assets/img/profile.png','star'=>50]);
        

        $romance = Genre::factory()->create(['genre_name'=>'romance','genre_slug'=>'romance']);
        $fantasy = Genre::factory()->create(['genre_name'=>'fantasy','genre_slug'=>'fantasy']);
        $action = Genre::factory()->create(['genre_name'=>'action','genre_slug'=>'action']);
        $school_life = Genre::factory()->create(['genre_name'=>'school life','genre_slug'=>'school life']);

        $theguyupstairs = Blog::factory()->create(['user_id'=>$mgmg->id,'title'=>'The Guy Upstaris','blogcoverphoto'=>'./assets/img/theguyupstairs.jpg','slug'=>'theguyupstairs','summary' => "ရိုဇီက သူမရဲ့ မိတ်ဆွေကို ရှာဖွေနေတယ်...။အပေါ်ထပ်မှာနေတဲ့လူက နည်းနည်းလေး သံသယ ဝင်စရာ ကောင်းတယ်။"]);
        $nicetomeetyou = Blog::factory()->create(['user_id'=>$aungaung->id,'title'=>'Nice To Meet You','blogcoverphoto'=>'./assets/img/nicetomeetyou.png','slug'=>'nicetomeetyou','summary'=> "လျှပ်ပြာလျှပ်ပြာနေတတ်တဲ့ တက္ကသိုလ်ကျောင်းသူလေး Mew ကတော့ တစ်စုံတစ်ယောက် ကျပျောက်ခဲ့တဲ့ ကျောင်းသားကတ်လေးကို ကောက်ရခဲ့ပါတယ်။ ဒါပေမဲ့ သူ့ရဲ့စနောက်လိုတဲ့စိတ်ကလေးကြောင့် ပိုင်ရှင်ဆီပြန်မပေးဘဲ သူ့ကျောင်းသားကတ်နဲ့ ကတ်အချင်းချင်းလဲထားလိုက်ပါတော့တယ်။ အဲ့ဒီနောက် ကျောင်းသားကတ်ပိုင်ရှင် Daze နဲ့ထိပ်ဆိုင်တွေ့ရမယ်လိုမသိရှာခဲ့တဲ့ Mew ကတော့သူ့ဘဝအတွက်အမှားကြီးကိုလုပ်ခဲ့မိတာလား? ဒါမှမဟုတ်အကောင်းဆုံးဆုံးဖြတ်ချက်ကိုချခဲ့မိတာလားဆိုတာ.... ရှာဖွေဖိုအတွက်ဒီချစ်စရာကောင်းတဲ့ရူးကြောင်ကြောင်လေးနှစ်ယောက်ရဲ့ဇာတ်လမ်းလေးကိုဖတ်ရှုလိုက်ရအောင်!💓"]);
        $revelationofyouth = Blog::factory()->create(['user_id'=>$aungaung->id,'title'=>'Revelation Of Youth','blogcoverphoto'=>'./assets/img/revelationofyouth.png','slug'=>'revelationofyouth','summary' => 'ဘာသာရေးဂိုဏ်းချုပ်ရဲ့သားနဲ့ အန္တရာယ်များတဲ့ အတူတကွနေထိုင်ခြင်းဟာ စတင်ခဲ့ပါပြီ။ Chungha တိုမိသားစု ယုံကြည်ရတဲ့ဦးလေးဖြစ်သူဆီက ငွေအလိမ်ခံလိုက်ရတဲ့နောက်မှာတော့ မိဘတွေက ထွက်ပြေးသွားတဲ့ ဦးလေးကိုရှာဖိုဆိုတဲ့ အကြောင်းပြချက်နဲ့ ယာယီအဆက်အသွယ် ဖြတ်သွားကြပါပြီး Chungha ကတော့ အိမ်အလွတ်တစ်လုံးမှာ တစ်ယောက်တည်းနေရဖို အကြောင်းဖန်လာပါတော့တယ်။ ဒါပေမဲ့ ဦးလေးဆီမှာ အလိမ်ခံလိုက်ရတာက Chungha တိုမိသားစုတင် မဟုတ်ပါဘူး။ ကိုယ်ပိုင်အုပ်ချုပ်ခွင့်ရခရိုင်ရဲ့ အကြီးဆုံး ဘာသာရေးဂိုဏ်းဖြစ်တဲ့ " နှစ်တစ်ထောင် ဥပုသ်အသင်းအဖွဲ " လည်း အပါအဝင်ပါပဲ။ အဲ့ဒီနောက်မှာတော့ ဘာသာရေးဂိုဏ်းချုပ်ရဲ့သားဖြစ်သူ " Yohan " က Chungha နေတဲ့အိမ်ကို ရောက်လာခဲ့ပါတယ်။ လက်ထဲမှာလည်း သူ့ခရီးဆောင်အိတ်ကြီးကို ဆွဲလာပြီး....။ ဘုရားရေ ဒီအတူတကွနေထိုင်မှုက အဆင်ပြေပါ့မလား။']);
        $revengeoflove = Blog::factory()->create(['user_id'=>$aungaung->id,'title'=>'Revenge Of Love','blogcoverphoto'=>'./assets/img/revengeoflove.jpg','slug'=>'revengeoflove','summary' => '“ငါတို...အရင်လို သူငယ်ချင်းတွေအဖြစ်နဲ့ပဲနေကြရအောင်" ခြောက်လကြာတွဲပြီးတဲ့နောက်မှာတော့ Sakura ဟာ သူ့ငယ်သူငယ်ချင်းဖြစ်တဲ့ Ren ဆီကနေ လမ်းခွဲတာခံခဲ့ရပါတယ်။ မေ့ပစ်နိုင်ဖိုအတွက် သူအကောင်းဆုံးကြိုးစားခဲ့ပေမဲ့ သူ့ရည်းစားဟောင်းကို အထက်တန်းကျောင်းပထမဆုံးတက်တဲ့နေ့မှာ တွေ့လိုက်ရတဲ့အခါမှာတော့ Ren အပေါ်ထားတဲ့ သူ့ခံစားချက်တွေကမပြောင်းလဲသေးဘူးဆိုတာ သူနားလည်လိုက်ပါတယ်။ သူတိုနှစ်ယောက်အတွက် ဒုတိယအခွင့်အရေးထပ်ရှိသေးတာလားဆိုတာ?']);

        $bloggenres = BlogGenre::factory()->create(['blog_id'=>$theguyupstairs->id,'genre_id'=>$romance->id]);
        $bloggenres2 = BlogGenre::factory()->create(['blog_id'=>$nicetomeetyou->id,'genre_id'=>$romance->id]);
        $bloggenres3 = BlogGenre::factory()->create(['blog_id'=>$revelationofyouth->id,'genre_id'=>$fantasy->id]);
        $bloggenres4 = BlogGenre::factory()->create(['blog_id'=>$revengeoflove->id,'genre_id'=>$fantasy->id]);
        $bloggenres5 = BlogGenre::factory()->create(['blog_id'=>$theguyupstairs->id,'genre_id'=>$action->id]);
        $bloggenres6 = BlogGenre::factory()->create(['blog_id'=>$theguyupstairs->id,'genre_id'=>$school_life->id]);
        
     
        
        $basic = StarPackage::factory()->create(['package_name' => "Basic",'star_amount' => 20,'extra_star_amount' => 2,'total_star_amount' => 22,'package_price' => 1000]);
        $broonze = StarPackage::factory()->create(['package_name' => "Broonze",'star_amount' => 60,'extra_star_amount' => 10,'total_star_amount' => 70,'package_price' => 3000]);
        $silver = StarPackage::factory()->create(['package_name' => "Silver",'star_amount' => 100,'extra_star_amount' => 20,'total_star_amount' => 120,'package_price' => 5000]);
        $gold = StarPackage::factory()->create(['package_name' => "Gold",'star_amount' => 200,'extra_star_amount' => 40,'total_star_amount' => 240,'package_price' => 10000]);
        $platinum = StarPackage::factory()->create(['package_name' => "Platinum",'star_amount' => 400,'extra_star_amount' => 100,'total_star_amount' => 500,'package_price' => 20000]);
        $diamond = StarPackage::factory()->create(['package_name' => "Diamond",'star_amount' => 800,'extra_star_amount' => 200,'total_star_amount' => 1000,'package_price' => 30000]);

    }
}
