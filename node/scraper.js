'use strict';

const Browser = require('browser');

const defaultOptions = {
	chromePath: '../storage/chrome',
	logPath: '../storage/logs',
	logFilename: 'reviews.log',
	loadImages: true,
	proxy: {
		server: 'switchproxy.proxify.net:7498',
		// server: 'proxy.crawlera.com:8010',
		// user: '7eab1b8370834ff8abb358556a34ea61',
		// password: null,
	}
};
const options = Object.assign({}, defaultOptions, JSON.parse(process.argv[2]));
const browser = new Browser(options);

(async() => {
	try {
	  	process.on('uncaughtException', (e) => {
	    	throw e;
	  	});

	  	process.on('unhandledRejection', (reason, p) => {
	    	throw String(reason);
	  	});

	  	await browser.init();

	  	let totalPages = 0, pageNumber = 1;
	  	let asin = options.asin;
        let promises = [], reviews = [];
        promises.push(browser.newPage().then(async page => {
            let $,
                id = '[' + asin + ']',
                url = 'https://www.amazon.com/product-reviews/' + asin + '/ref=cm_cr_getr_d_paging_btm_1?pageNumber=1&reviewerType=all_reviews&sortBy=recent';
            await browser.disableRequests(page, ['image', 'font', 'stylesheet', 'script', 'xhr']);
            browser.log(id + 'Opening the product reviews on page ' + pageNumber + ' ...');

            if (browser.options.debug) {
                browser.log(id + url);
            }

            await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60000 });

            $ = await browser.jQuery(page);

            if ($('a[href="/dogsofamazon"]').length != 1) {
                const totalPages = $('#cm_cr-pagination_bar .page-button').length > 0 ?
                                        +$('#cm_cr-pagination_bar .page-button').last().text().trim() : 1;

                for (pageNumber = 1; pageNumber <= totalPages; pageNumber++) {
                    browser.log(id + 'Scraping product reviews of ' + asin + ' on page ' + pageNumber + ' ...');
                    url = 'https://www.amazon.com/product-reviews/' + asin + '/ref=cm_cr_getr_d_paging_btm_' + pageNumber + '?pageNumber=' + pageNumber + '&reviewerType=all_reviews&sortBy=recent'
                    
                    await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60000 });
                    await page.waitForSelector('#cm_cr-review_list');
                    
                    $ = await browser.jQuery(page);

                    browser.log(id + 'Collecting reviews data ...');
                    $('#cm_cr-review_list .a-section.review').each(function() {
                        let helpfulVotesCount = $(this).find('span.review-votes').text().trim();
                        helpfulVotesCount = helpfulVotesCount.split(' ')[0];
                        if (helpfulVotesCount.substr(0, 3) == 'One') helpfulVotesCount = 1;

                        let childAsin, childName, childNode = $(this).find('.review-data.review-format-strip > a.a-link-normal');
                        if (childNode.length > 0) {
                            childAsin = childNode.attr('href').split('/')[5];
                            childName = childNode.text().trim();
                        }

                        let reviewTitleDom = $(this).find('.review-title');

                        let reviewDate = new Date($(this).find('.review-date').first().text().trim().replace('on ', ''));

                        reviews.push({
                            child_asin: childAsin,
                            child_name: childName,
                            review_link: 'https://www.amazon.com' + reviewTitleDom.attr('href'),
                            title: reviewTitleDom.text().trim(),
                            score: parseInt($(this).find('.review-rating').text().trim()),
                            body: $(this).find('.review-text').html(),
                            review_date: reviewDate.getFullYear() + '-' + (reviewDate.getMonth() + 1) + '-' + reviewDate.getDate(),
                            author: $(this).find('a.author').text().trim(),
                            author_link: $(this).find('a.author').attr('href'),
                            number_of_comments: +$(this).find('span.review-comment-total').length,
                            has_photo: $(this).find('.review-image-container').length > 0,
                            has_video: $(this).find('.video-block').length > 0,
                            verified: $(this).find('.review-data.review-format-strip > span.a-declarative').text() == 'Verified Purchase',
                            helpful_votes_count: +helpfulVotesCount,
                        });
                    });
                        
                    browser.log(id + 'Found reviews: ' + reviews.length + ' items.');
                }
            }

            await page.close();
        }));

        if (promises.length > 0) {
            await Promise.all(promises);
            await browser.sleep(2, 5);
        }

		browser.log('Reviews: ' + JSON.stringify(reviews), true);

		if (! browser.options.debug) {
	  		console.log(JSON.stringify(reviews));
			await browser.close();
		}
	} catch (err) {
		browser.log(err, true);

		if (! browser.options.debug) {
			console.log(JSON.stringify({error: String(err)}));

			await browser.close().catch();
		}
	}
})();