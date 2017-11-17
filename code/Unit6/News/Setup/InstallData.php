<?php

namespace Unit6\News\Setup;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Unit6\News\Model\Data\News;
use Unit6\News\Model\Data\NewsFactory;

class InstallData implements InstallDataInterface
{
    /* @var NewsFactory */
    private $newsFactory;

    /* @var EntityManager */
    private $em;

    /**
     * InstallData constructor.
     * @param NewsFactory $newsFactory
     * @param EntityManager $em
     */
    public function __construct(NewsFactory $newsFactory, EntityManager $em)
    {
        $this->newsFactory = $newsFactory;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $fixtures = $this->getContentFixture();
        $data = [
            ['title' => 'News 1', 'content' => $fixtures['content1']],
            ['title' => 'News 2', 'content' => $fixtures['content2']],
            ['title' => 'News 3', 'content' => $fixtures['content3']],
            ['title' => 'News 4', 'content' => $fixtures['content4']]
        ];

        foreach ($data as $item){
            /* @var $newsData News */
            $newsData = $this->newsFactory->create();
            $newsData->setAllData($item);
            $this->em->save($newsData);
        }
    }

    protected function getContentFixture()
    {
        return [
            'content1' =>
            '<p>Born rejects master system to will must laborious obtain advantage are, <b>anyone explorer chooses: it</b> you, example a are occur procure. There <a href="#">chooses enjoy example</a>, chooses, rationally except, can ever mistaken expound laborious but, procure because will because rejects.</p> ',

            'content2' =>
            '<p>Chooses produces who or, take has again occur can a pursues which occur denouncing, because this denouncing do <a href="#">those, laborious but ever one.</a> System can encounter to but there rejects, but denouncing actual explorer desires resultant, that denouncing from laborious idea, or pain this. Some there nor great from give anyone, teachings loves with enjoy itself procure explain pain because which must who how can <a href="#">with that.</a> Extremely man procure or to will master nor right, advantage, us pleasure there how, us that right pursue consequences example except circumstances the. Rejects born know actual advantage happiness denouncing those are, the truth are it <i>enjoy expound</i> because resultant actual procure rationally.</p><p>Know ever pursue born give <b>exercise ever enjoy circumstances</b>, the it dislikes pain, take find dislikes not. Pursues some example do builder will a again teachings but. Because: <a href="#">anyone a</a> itself of toil, will no, explain ever occasionally itself rejects, not is it system actual encounter. But truth&nbsp;&mdash; find to are itself&nbsp;&mdash; to annoying us this complete in do, avoids do to&nbsp;&mdash; this. Master do avoids anyone right is builder denouncing <i>actual are annoying advantage.</i> Happiness occasionally it mistaken resultant was there him laborious: it builder to again enjoy trivial. Teachings of&nbsp;&mdash; obtain, this: annoying how, pain which has give undertakes again <i>from undertakes</i> explorer. Pursues denouncing you circumstances actual know, explain find will there occasionally&nbsp;&mdash; annoying, can procure anyone, this, except ever anyone.</p> ',

            'content3' =>
            '<p>Actual enjoy pursues from exercise enjoy master: actual with encounter <b>some complete&nbsp;&mdash; great</b> anyone&nbsp;&mdash; us undertakes denouncing, one of occur builder. Loves undertakes some know fault trivial expound, do undertakes any who human master human there, master, any, no all extremely there rationally any builder. Praising desires occur great explain, but praising has, undertakes complete, human: that, a.</p><p>Obtain trivial resultant pain trivial system <i>the or take</i>, builder exercise explain do advantage again <i>pursues complete.</i> In human there example pain except fault are of you of all encounter from this toil find toil him it, system. Who to annoying us: encounter one annoying truth right which. Are can painful dislikes explorer do expound right trivial account are anyone trivial builder idea those physical <i>in, rationally complete</i> procure. Rejects find undertakes happiness laborious builder is, example a denouncing actual those resultant there, system complete one occasionally man avoids. Pain circumstances painful except <i>or master, can</i>, him fault rejects, there to those, occasionally there: <b>except give</b> that explain.</p> ',

            'content4' =>
            '<p>Dislikes praising occasionally there laborious toil laborious <a href="#">expound, there</a> <b>is how laborious</b> anyone example painful any pursue account advantage happiness. Occasionally know take actual any ever occasionally are us loves rejects and pain man, great undertakes. Painful, human rejects explorer human man resultant procure pain there do those who right, will, that. Idea loves denouncing are annoying will encounter avoids occasionally expound: annoying, the or that painful teachings. Pain is must actual him happiness all procure in those, again desires.</p> '
        ];
    }
}
