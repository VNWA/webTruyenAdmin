const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

// Hàm để crawl dữ liệu từ nhiều sản phẩm
const crawlMultipleProducts = async (url) => {
    try {
        const { data } = await axios.get(url, {
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36',
                'Cookie': 'toonily-mature=1', // Thiết lập cookie để tắt Family Mode
            },
        });

        console.log('Fetched data from:', url);

        const $ = cheerio.load(data);
        const products = [];

        $('.page-item-detail.manga').each((i, elem) => {
            if (i <= 0) {
                const link = $(elem).find('a').attr('href');
                const title = $(elem).find('.post-title h3 a').text();
                const image = $(elem).find('img').attr('data-src');

                products.push({
                    link,
                    title,
                    image,
                    chapters: [], // Khởi tạo chapters rỗng
                });
            }
        });

        // Lấy chương cho từng sản phẩm
        for (const product of products) {
            product.chapters = await getChapters(product.link);
        }

  

        return products;
    } catch (error) {
        console.error('Error fetching the products:', error);
    }
};


// Hàm để lấy danh sách các chapter từ trang chi tiết sản phẩm
const getChapters = async (productLink) => {
    try {
        const { data } = await axios.get(productLink);
        console.log('Fetched data from chapter:', productLink);
        const $ = cheerio.load(data);
        const chapters = [];

        $('ul.main.version-chap.no-volumn li.wp-manga-chapter').each((i, elem) => {
            const chapterLink = $(elem).find('a').attr('href');
            const chapterTitle = $(elem).find('a').text().trim();

            chapters.push({
                link: chapterLink,
                title: chapterTitle,
                images: [], // Khởi tạo images rỗng
            });
        });

        // Lấy hình ảnh cho từng chapter
        for (const chapter of chapters) {
            chapter.images = await getImagesFromChapter(chapter.link);
        }

        return chapters; // Đảo ngược thứ tự
    } catch (error) {
        console.error('Error fetching the chapters:', error);
    }
};

// Hàm để lấy danh sách các link ảnh từ trang chapter
const getImagesFromChapter = async (chapterLink) => {
    try {
        const { data } = await axios.get(chapterLink);
        console.log('Fetched data from images:', chapterLink);
        const $ = cheerio.load(data);
        const images = [];

        $('div.reading-content img').each((i, elem) => {
            const imageSrc = $(elem).attr('data-src');
            if (imageSrc) {
                images.push(imageSrc);
            }
        });

        return images;
    } catch (error) {
        console.error('Error fetching images from chapter:', error);
    }
};
const sendProductsToLaravel = async (products,url_import) => {
    try {
        // Gửi mảng products đến Laravel
        const response = await axios.post(url_import, {products: products});
        console.log('Products sent to Laravel:', response);
    } catch (error) {
        console.error('Error sending products to Laravel:', error);
    }
};
// Chạy crawler
(async () => {
    const url_web = 'https://toonily.com/webtoon';
    const url_import = 'http://127.0.0.1:8000/vnwa-asdghuajsdg/import-crawl-18-products';
    const products = await crawlMultipleProducts(url_web);
    await sendProductsToLaravel(products,url_import);

})();
