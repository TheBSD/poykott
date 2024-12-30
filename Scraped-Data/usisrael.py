# encoding: utf-8
import os
from scrapy import Spider, Request
from scrapy.crawler import CrawlerProcess


class SpiderUsisrael(Spider):
    name = "usisrael"

    def start_requests(self):
        yield Request('https://www.usisrael.co/unicorn-tracker', callback=self.parse)

    def parse(self, response):
        for index in range(1, len(response.xpath('//div[@class="ut-tab-content"]')) + 1):
            title = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[@class="ut-tab-title"]/text()').get()
            valuation = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[div[text()="Valuation*"]]/div[2]/text()').get()
            uni_date = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[div[text()="Unicorn Date"]]/div[2]/text()').get()
            website = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[div[text()="Website"]]/a/@href').get()
            state = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[div[text()="State"]]/div[2]/text()').get()
            solution = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[div[text()="Solution"]]/div[2]/text()').get()
            jobs_created = response.xpath(
                f'(//div[@class="ut-tab-content"])[{index}]//div[div[text()="Jobs Created Worldwide*"]]/div[2]/text()').get()

            yield {
                'title': title,
                'valuation': valuation,
                'uni_date': uni_date,
                'website': website,
                'state': state,
                'solution': solution,
                'jobs_created': jobs_created
            }


if __name__ == "__main__":
    Settings = {

        'FEEDS': {os.path.join('result folder', 'usisrael-unicorn-tracker.csv'): {
            'format': 'csv',
            "overwrite": True
        },
            os.path.join('result folder', 'usisrael-unicorn-tracker.json'): {
                'format': 'json',
                "overwrite": True
            }
        },
        'USER_AGENT': 'Free Palestine',
        'LOG_LEVEL': 'INFO',
    }
    process = CrawlerProcess(Settings)
    process.crawl(SpiderUsisrael)
    process.start()

"""
 Created by [Ahmed Ellaban](https://upwork.com/freelancers/ahmedellban)
وَسَلَامٌ عَلَى الْمُرْسَلِينَ وَالْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ 
"""
