const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

// Hàm để crawl dữ liệu từ nhiều sản phẩm
const crawlMultipleProducts = async (url) => {
    try {
        const { data } = await axios.get(url, {
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36',
            },
        });

        console.log('Fetched data from:', url);

        const $ = cheerio.load(data);
        const products = [];

        $('.page-item').each((i, elem) => {
            if (i <= 3) {
                const link = $(elem).find('a').attr('href');
                const title = $(elem).find('h3.tt a').text();
                const image = $(elem).find('.thumb-manga a img').attr('data-src');

                products.push({
                    link,
                    title,
                    image,
                    chapters: [], // Khởi tạo chapters rỗng
                });
            }
        });
        let n = 0;
        // Lấy chương cho từng sản phẩm
        for (const product of products) {

            product.chapters = await getChapters('https://manga18fx.com/' + product.link);
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

        $('ul.row-content-chapter li.a-h').each((i, elem) => {
            const chapterLink = $(elem).find('a').attr('href');
            const chapterTitle = $(elem).find('a').text().trim();

            chapters.push({
                link: chapterLink,
                title: chapterTitle,
                images: [], // Khởi tạo images rỗng
            });
        });

        for (const chapter of chapters) {


            chapter.images = await getImagesFromChapter('https://manga18fx.com/' + chapter.link);
        }

        return chapters.reverse(); // Đảo ngược thứ tự
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

        $('div.read-content .page-break img').each((i, elem) => {
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
const sendProductsToLaravel = async (products, url_import) => {
    try {
        // Gửi mảng products đến Laravel
        const response = await axios.post(url_import, { products: products });
        console.log('Products sent to Laravel:', response);
    } catch (error) {
        console.error('Error sending products to Laravel:', error);
    }
};
// Chạy crawler
(async () => {
    const url_web = 'https://manga18fx.com/manga-genre/raw';
    const url_import = 'https://demo1.vinawebapp.com/vnwa-asdghuajsdg-import-crawl/manga18fx/import-crawl-18';
    const products = await crawlMultipleProducts(url_web);
    await sendProductsToLaravel(products, url_import);

})();
