/**
 * Persian UI copy for the public website.
 *
 * Keep navigation and section labels here so the wording can be changed
 * without editing page components. This is intentionally a plain object for
 * now; a locale switcher can wrap it later if the public language strategy
 * becomes bilingual.
 */
const fa = {
    nav: {
        announcements: 'اخبار و اطلاعیه‌ها',
        posts: 'مطالب',
        postsAndSeries: 'مطالب و مجموعه‌ها',
        series: 'مجموعه‌ها',
        courses: 'دوره‌ها و برنامه‌ها',
        books: 'کتاب‌ها',
        about: 'درباره ما',
        registration: 'ثبت‌نام در مؤسسه',
        contact: 'ارتباط با ما',
        donation: 'حمایت مالی',
    },
    sections: {
        announcements: 'اخبار و اطلاعیه‌ها',
        postsAndSeries: 'مطالب و مجموعه‌ها',
        courses: 'دوره‌ها و برنامه‌ها',
        books: 'کتاب‌ها',
        about: 'درباره ما',
        registration: 'ثبت‌نام در مؤسسه',
        contact: 'ارتباط با ما',
        donation: 'حمایت مالی از اندیشکده',
    },
    contentTypes: {
        post: 'مطلب',
        series: 'مجموعه',
        course: 'دوره',
        book: 'کتاب',
        announcement: 'اطلاعیه',
    },
    actions: {
        showMore: 'نمایش بیشتر',
    },
} as const;

export default fa;
export { fa };
