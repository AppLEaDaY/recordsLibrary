<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Record;
use AppBundle\Form\RecordType;

use Symfony\Component\HttpFoundation\Session\Session;


//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\Operations\Search;
use ApaiIO\ApaiIO;
use Symfony\Component\HttpFoundation\Response;
//use rlBundle\Entity\Record;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\HttpFoundation\Request;









/**
 * Record controller.
 *
 * @Route("/records")
 */
class RecordController extends Controller
{


    /**
     * Provides a simple entry page.
     *
     * @Route("/", name="records_index")
     */
     public function indexAction () {
       return $this->render('record/index.html.twig');
     }



    /**
     * Lists all Record entities.
     *
     * @Route("/list/{substring}", name="records_list", defaults={"substring": ""})
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $substring = $request->query->get('substring');

        if ($substring == '') {
          $records = $em->getRepository('AppBundle:Record')->findAll();
        } else {
          $records = $em->getRepository('AppBundle:Record')
                        ->createQueryBuilder('qb')
                        ->where('qb.artist LIKE :substring')
                        ->orwhere('qb.title LIKE :substring')
                        ->setParameter('substring', '%'.$substring.'%')
                        ->getQuery()
                        ->getResult();
          //$records = $em->getRepository('AppBundle:Record')->findByArtist($substring);
        }
        return $this->render('record/list.html.twig', array(
            'records' => $records,
        ));
    }

    /**
     * Creates a new Record entity.
     *
     * @Route("/new", name="record_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $record = new Record();
        $form = $this->createForm('AppBundle\Form\RecordType', $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($record);
            $em->flush();

            return $this->redirectToRoute('record_show', array('id' => $record->getId()));
        }

        return $this->render('record/new.html.twig', array(
            'record' => $record,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Record entity.
     *
     * @Route("/{id}", name="record_show")
     * @Method("GET")
     */
    public function showAction(Record $record)
    {
        $deleteForm = $this->createDeleteForm($record);

        return $this->render('record/show.html.twig', array(
            'record' => $record,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Record entity.
     *
     * @Route("/{id}/edit", name="record_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Record $record)
    {
        // Two forms are needed, one is for editing,
        //the other one is to delete the same record.
        $deleteForm = $this->createDeleteForm($record);
        $editForm = $this->createForm('AppBundle\Form\RecordType', $record);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $tracksListsInString = $editForm['tracksListsInString']->getData();
            $tracksLists = array();
            $tracksLists = split('\|\|', $tracksListsInString);
            $index = 0;
            foreach ($tracksLists as $trackList) {
              $tracksLists[$index++] = split('\|', $trackList);
            }
            //$record->setTracksLists(json_encode($tracksLists));
            // TODO: understanding why json encoding is to be avoided,
            // otherwise the structure comes out with escape quotes...
            $record->setTracksLists($tracksLists);
            //return(new Response($record->getTracksLists()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($record);
            $em->flush();

            return $this->redirectToRoute('record_show', array('id' => $record->getId()));
        }

        return $this->render('record/edit.html.twig', array(
            'record' => $record,
            /* tracks lists array is no more bound to the entity "record",
            since twig cannot handle array to string conversions */
            'tracksLists' => $record->getTracksLists(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Record entity.
     *
     * @Route("/{id}", name="record_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Record $record)
    {
        $form = $this->createDeleteForm($record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($record);
            $em->flush();
        }

        return $this->redirectToRoute('records_list');
    }

    /**
     * Creates a form to delete a Record entity.
     *
     * @param Record $record The Record entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Record $record)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('record_delete', array('id' => $record->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }







    /* AAA */
    /**
     * @Route(
     *    path = "/insert/insertByUPC/{upc}",
     *    name = "insertByUPC",
     *    defaults={"upc" = "012345678901"}
     * )
     */
    public function insertByUPCAction(Request $request, $upc) {

      /* TODO: DISPLAYING A MESSAGE WHEN UPC VALUE HAS NOT BEEN FOUND */

      /* DISPLAYING THE INPUT FORM */
      $upcQuery = array('upc' => '');
      $form = $this->createFormBuilder($upcQuery)
      ->setAction($this->generateUrl('insertByUPC'))
      ->setMethod('GET')
      ->add('upc', TextType::class)
      ->add('save', SubmitType::class, array('label' => 'UPC search and possible insertion'))
      ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted()/* || ($upc*/) {

        // Form has been submitted or upc has been passed in the URL

        $data = $form->getData();
        if ($data['upc'] != '') {
          $upc = $data['upc'];
        }
        // ... perform some action, such as saving the task to the database
        //$resString = print_r($data);
        //return(new Response($resString));
        /*
        searching by UPC,
        if found creating an object and
        setting its properties accordingly
        */

        // TODO:
        // moving configuration data outside in a configuration file
        $conf = new GenericConfiguration();
        $conf
        ->setCountry('com')
        ->setAccessKey('AKIAJD57F37W2KGLXEVQ')
        ->setSecretKey('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')
        ->setAssociateTag('quercusroburn-20')
        ->setRequest('\ApaiIO\Request\Soap\Request');

        $lookup = new Lookup();
        $lookup->setIdType('UPC');
        //$lookup->setItemId('724384062026');
        //$lookup->setItemId('724382912521');
        $lookup->setItemId($upc);
        $lookup->setResponseGroup(array('ItemAttributes', 'Images', 'Tracks')); // More detailed information

        $apaiIo = new ApaiIO($conf);
        $response = $apaiIo->runOperation($lookup);


        $logger = $this->get('logger');

        // Cozy variable to shorten object invocation
        $item = $response->Items->Item;

        // TODO:
        // refining the way to decide if a result has been found
        if (property_exists($item->ItemAttributes, 'UPC')) {
          /* Item with that UPC has been found */
          $newRecord = new Record();

          $newRecord->setUpc($item->ItemAttributes->UPC);
          $newRecord->setAsin($item->ASIN);
          $newRecord->setCoverImageUrl($item->LargeImage->URL);
          if (property_exists($item->ItemAttributes, 'Brand')) {
            $newRecord->setRecordLabel($item->ItemAttributes->Brand);
          } else {
            $newRecord->setRecordLabel($item->ItemAttributes->Label);
          }

          $newRecord->setYear(substr($item->ItemAttributes->ReleaseDate, 0, 4));
          $logger->info('Vediamo: ' . json_encode($item));
          if (property_exists($item->ItemAttributes, 'Artist')) {
            if (is_array($item->ItemAttributes->Artist)) {
              // MEMO
              // "implode" and "join" are the same, they are alias
              $newRecord->setArtist(implode(', ', $item->ItemAttributes->Artist));
            } else {
              $newRecord->setArtist($item->ItemAttributes->Artist);
            }
          } else if (property_exists($item->ItemAttributes, 'Creator')) {
            // UPC: 828767523224
            // ASIN: B000C6NONM
            // Fabrizio De André, "In direzione ostinata e contraria"
            $newRecord->setArtist($item->ItemAttributes->Creator->_);
            // "Creator":{"_":"Fabrizio De Andr\u00e9","Role":"Performer"},
            // TODO
            // Porting this mechanism in searchByText
          } else {
            // Couldn't extract artist info
          }

          $newRecord->setTitle($item->ItemAttributes->Title);
          $newRecord->setMediaType($item->ItemAttributes->Binding);
          $newRecord->setMediaCount($item->ItemAttributes->NumberOfItems);

          // each media item has a track list
          $trackLists = array();

          if (property_exists($item, 'Tracks')) { // Having the number of items does not mean tracks info is available...

            // When number of media/items is greater than 1, but
            // the tracks list is just one
            // (it happened at least once with B000B66OVW, see EX03 below)
            // then ->Tracks->Disc is not an array at it should be
            // when more than one media are actually present
            $oneListOnly = 0;
            if (!is_array($item->Tracks->Disc)) {
              $logger->info('The web service reported ' . $newRecord->getMediaCount() . ' media, but all tracks are in just one list.');
              $oneListOnly = 1;
            }

            if (($item->ItemAttributes->NumberOfItems < 2) || $oneListOnly) {

              //$newRecord->setMediaCount(1);

              // One item only, one list, see EX00 at the bottom
              $totalTracks = sizeof($item->Tracks->Disc->Track);
              for ($i = 0; $i < $totalTracks; $i++) {
                $tracksLists[0][] = $item->Tracks->Disc->Track[$i]->_;
              }

            } else { // More than one media (EX01 ath the bottom)

              for ($j = 0; $j < $item->ItemAttributes->NumberOfItems; $j++) {
                $totalTracks = sizeof($item->Tracks->Disc[$j]->Track);
                for ($i = 0; $i < $totalTracks; $i++) {
                  $tracksLists[$j][] = $item->Tracks->Disc[$j]->Track[$i]->_;
                }
              }

            }

          } else {
            // Tracks info is not available (no property "Tracks" in the object)
            $logger->info('Tracks info is not available');
          }

          $newRecord->setTracksLists($tracksLists);

          $em = $this->getDoctrine()->getManager();

          $em->persist($newRecord);
          $em->flush();

          return $this->redirectToRoute('record_show', array('id' => $newRecord->getId()));

        } else {
          // TODO: DISPLAYING message telling the upc has not been found
          return (new Response ('upc not found'));
        }

      } else { // Form has not been submitted and UPC has not been passed via URL
        return $this->render('AppBundle:Record:insertByUPC.html.twig', array('form' => $form->createView()));
      }

    } // insertByUPCAction



    /**
     * @Route(
     *    path = "/search/searchByText/{text}",
     *    name = "searchByText",
     *    defaults={"text" = "012345678901"}
     * )
     */
    public function searchByTextAction (Request $request) {

      /* TODO: DISPLAYING A MESSAGE WHEN NOTHING HAS BEEN FOUND */

      /* DISPLAYING THE INPUT FORM */
      $textQuery = array('text' => '');

      $form = $this->createFormBuilder($textQuery)
      ->setAction($this->generateUrl('searchByText'))
      ->setMethod('GET')
      ->add('text', TextType::class, array('label' => 'Keywords', 'attr' => array('class' => 'form-control')))
      ->add('submit', SubmitType::class, array('label' => 'Look for possible equivalent items', 'attr' => array('class' => 'btn btn-default')))
      ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted()) {

        // Form has been submitted or some text has been passed in the URL

        $data = $form->getData();
        if ($data['text'] != '') {
          $text = $data['text'];
        }

        // ... perform some action, such as saving the task to the database
        //$resString = print_r($data);
        //return(new Response($resString));
        /*
        searching by text,
        if found displaying what has been found
        */

        // TODO:
        // moving configuration data outside in a configuration file
        $conf = new GenericConfiguration();
        $conf
        ->setCountry('com')
        ->setAccessKey('AKIAJD57F37W2KGLXEVQ')
        ->setSecretKey('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')
        ->setAssociateTag('quercusroburn-20')
        ->setRequest('\ApaiIO\Request\Soap\Request');


        $query = new Search();
        $query->setKeywords($text);
        $query->setCategory('Music');


        $query->setResponseGroup(array('ItemAttributes', 'Images', 'Tracks')); // More detailed information

        $apaiIo = new ApaiIO($conf);
        $response = $apaiIo->runOperation($query);


        $logger = $this->get('logger');

        $records = array();

        if(property_exists($response, 'Items')) {

          // handlig the case when one item only is returned,
          // in that case $response->Items->Item is not an array!!!
          $results = array();
          if (is_array($response->Items->Item)) {
            $results = $response->Items->Item;
          } else {
            $results[] = $response->Items->Item;
          }

          foreach ($results as $item) {

            $logger->info('----------------------------');

            $record = new Record;

            // ASIN
            $logger->info("ASIN: $item->ASIN");
            $record->setAsin($item->ASIN);

            // TODO:
            // now getting UPC just for testing purposes,
            // this should be stopped...
            if (property_exists($item->ItemAttributes, 'UPC')) {
              $logger->info('UPC: ' . $item->ItemAttributes->UPC);
            } else {
              $logger->info('NO UPC!');
            }

            // Artist
            // Structure changes if several artists are listed
            if (property_exists($item->ItemAttributes, 'Artist')) {
              if(!(is_array($item->ItemAttributes->Artist))) {
                $record->setArtist($item->ItemAttributes->Artist);
              } else {
                $logger->info('Here it is the array: ' . print_r($item->ItemAttributes->Artist, true) .  ' - Seen as: ' . gettype($item->ItemAttributes->Artist) . ' - size: ' . sizeof($item->ItemAttributes->Artist));
                $artist = 'Various artists (';
                for ($i = 1; $i < sizeof($item->ItemAttributes->Artist); $i++) {
                  $artist .= $item->ItemAttributes->Artist[$i];
                  if ($i < (sizeof($item->ItemAttributes->Artist) - 1)) {
                    $artist .= ', ';
                  }
                }
                $artist .= ')';
                $record->setArtist($artist);
                $logger->info('Values: ' . join(' - ', $item->ItemAttributes->Artist));
              }
              $logger->info('Artist: ' . $record->getArtist());
            }

            // title
            $record->setTitle($item->ItemAttributes->Title);
            $logger->info('Title: ' . $record->getTitle());

            // label
            if (property_exists($item->ItemAttributes, 'Brand')) {
              $record->setRecordLabel($item->ItemAttributes->Brand);
            } else if (property_exists($item->ItemAttributes, 'Label')) {
              $record->setRecordLabel($item->ItemAttributes->Label);
            } else {
              //
            }
            $logger->info('Label: ' . $record->getRecordLabel());

            // Year
            if (property_exists($item->ItemAttributes, 'ReleaseDate')) {
              $record->setYear(substr($item->ItemAttributes->ReleaseDate, 0, 4));
              $logger->info('Label: ' . $record->getYear());
            } else {
              $logger->info('\'ReleaseDate\' not available, so dunno how to get the year...');
            }

            // Media count
            if (property_exists($item->ItemAttributes, 'NumberOfItems')) {
              $record->setMediaCount($item->ItemAttributes->NumberOfItems);
              $logger->info('Media count: ' . $record->getMediaCount());
            } else if (property_exists($item->ItemAttributes, 'NumberOfDiscs')) {
              $record->setMediaCount($item->ItemAttributes->NumberOfDiscs);
              $logger->info('Media count: ' . $record->getMediaCount());
            } else {
              //$logger->info(print_r($item->ItemAttributes, true));
              $logger->info('Number of media not available. Now what?! Tentatively I guess the value \'1\'.');
              $record->setMediaCount('1');
              $item->ItemAttributes->NumberOfItems = '1';
            }

            // Media type
            $record->setMediaType($item->ItemAttributes->Binding);
            $logger->info('Media type: ' . $record->getMediaType());


            // Cover image (it may be unavailable)
            if (property_exists($item, 'LargeImage')) {
              $record->setCoverImageUrl($item->LargeImage->URL);
              $logger->info('Cover image URL: ' . $record->getCoverImageUrl());
            }



            // each media item has a track list
            $trackLists = array();

            if (property_exists($item, 'Tracks')) { // Tracks info is not for granted...


              // When number of media/items is greater than 1, but
              // the tracks list is just one
              // (it happened at least once with B000B66OVW, see EX03 below)
              // then ->Tracks->Disc is not an array at it should be
              // when more than one media are actually present
              $oneListOnly = 0;
              if (!is_array($item->Tracks->Disc)) {
                $logger->info('The web service reported ' . $record->getMediaCount() . ' media, but all tracks are in just one list.');
                $oneListOnly = 1;
              }

              if (($record->getMediaCount() < 2) || $oneListOnly) {

                  // one item only, one list

                  if (property_exists($item->Tracks, 'Disc')) {
                    $totalTracks = sizeof($item->Tracks->Disc->Track);
                    for ($i = 0; $i < $totalTracks; $i++) {
                      $tracksLists[0][] = $item->Tracks->Disc->Track[$i]->_;
                    }
                  } else {
                    $logger->info('Couldn\'t find "Disc"...   ' . print_r($item->Tracks, true));
                  }

              } else {

                  for ($j = 0; $j < $record->getMediaCount(); $j++) {

                    $totalTracks = sizeof($item->Tracks->Disc[$j]->Track);
                    for ($i = 0; $i < $totalTracks; $i++) {
                      $tracksLists[$j][] = $item->Tracks->Disc[$j]->Track[$i]->_;
                    }

                  }

              }

              $record->setTracksLists($tracksLists);

            } else {
              // Tracks info is not available (no property "Tracks" in the object)
              $logger->info('Tracks info is not available');
            }


            // Appending the record to the list of possible records
            $records[] = $record;

          }

        }

        // TODO:
        // saving the array of results in session
        $session = new Session();
        //$session->start();

        $session->set('results', $records);
        $logger->info('Session ID: ' . $session->getId());

        // Returning the list of the results
        // TODO:
        // handling in the template the id of each record, so that in can be passed
        // afterwards for insertion and recovered from session without re-fetching
        // its data
        return $this->render('AppBundle:Record:results.html.twig', array('records' => $records));

      }

      // The form has not been submitted, displaying the form
      return $this->render('AppBundle:Record:searchByText.html.twig', array('form' => $form->createView()));


    } // searchByTextAction


    /**
     * @Route(
     *    path = "/session/checksession/{id}",
     *    name = "checksession",
     *    defaults={"id" = "0"}
     * )
     */
    public function checkSessionAction (Request $request, $id) {
      $session = new Session();
      //$session->start();

      $results = $session->get('results');

      //$id = $request->request->get('id');
      //$id = 0;
      return (new Response(print_r($results[$id], true)));
      //return (new Response(print_r($session, true)));
      //return (new Response($session->getId()));
    }


    /**
     * @Route(
     *    path = "/insert/insertRecord/{resultId}",
     *    name = "insertRecord"
     * )
     */
    public function insertRecordAction (Request $request, $resultId) {

      $session = new Session();

      $record = $session->get('results')[$resultId];
      $em = $this->getDoctrine()->getManager();
      $em->persist($record);
      $em->flush();

      return $this->redirectToRoute('record_show', array('id' => $record->getId()));

    }


} // class RecordController


/* EXAMPLE 02
{"Disc":[
  {"Track":
  [
    {"_":"The Three Of Us","Number":1},
    {"_":"Whipping Boy","Number":2},
    {"_":"Breakin' Down","Number":3},
    {"_":"Don't Take That Attitude To Your Grave","Number":4},
    {"_":"Waiting On An Angel","Number":5},
    {"_":"Mama's Got A Girlfriend Now","Number":6},
    {"_":"Forever","Number":7},
    {"_":"Like A King","Number":8},
    {"_":"Pleasure And Pain","Number":9},
    {"_":"Walk Away","Number":10},
    {"_":"How Many Miles Must We March","Number":11},
    {"_":"Welcome To The Cruel World","Number":12},
    {"_":"I'll Rise","Number":13}
    ],
  "Number":1},
  {"Track":
      [
        {"_":"Oppression","Number":1},
        {"_":"Ground On Down","Number":2},
        {"_":"Another Lonely Day","Number":3},
        {"_":"Please Me Like You Want To","Number":4},
        {"_":"Gold To Me","Number":5},
        {"_":"Burn One Down","Number":6},
        {"_":"Excuse Me Mr","Number":7},
        {"_":"People Lead","Number":8},
        {"_":"Fight For Your Mind","Number":9},
        {"_":"Give A Man A Home","Number":10},
        {"_":"By My Side","Number":11},
        {"_":"Power Of The Gospel","Number":12},
        {"_":"God Fearing Man","Number":13},
        {"_":"One Road To Freedom","Number":14}],
  "Number":2},
  {"Track":
      [
        {"_":"Alone","Number":1},
        {"_":"The Woman In You","Number":2},
        {"_":"Less","Number":3},
        {"_":"Two Hands Of A Prayer","Number":4},
        {"_":"Please Bleed","Number":5},
        {"_":"Suzie Blue","Number":6},
        {"_":"Steal My Kisses","Number":7},
        {"_":"Burn To Shine","Number":8},
        {"_":"Show Me A Little Shame","Number":9},
        {"_":"Forgiven","Number":10},
        {"_":"Beloved One","Number":11},
        {"_":"In The Lord's Arms","Number":12}],
  "Number":3}
  ]
}

*/

/* EX03

B000B66OVW: reported media count is 2, but ->Tracks->Disc is not an array,
so considering it as an array (more than one media branch) produces an error_log

stdClass Object (

[Disc] => stdClass Object (
  [Track] => Array (
    [0] => stdClass Object ([_] => The Three Of Us  [Number] => 1)
    [1] => stdClass Object ( [_] => Whipping Boy [Number] => 2 )
    [2] => stdClass Object ( [_] => Breakin' Down [Number] => 3 )
    [3] => stdClass Object ( [_] => Don't Take That Attitude To Your Grave [Number] => 4 )
    [4] => stdClass Object ( [_] => Waiting On An Angel [Number] => 5 )
    [5] => stdClass Object ( [_] => Mama's Got A Girlfriend Now [Number] => 6 )
    [6] => stdClass Object ( [_] => Forever [Number] => 7 )
    [7] => stdClass Object ( [_] => Like A King [Number] => 8 )
    [8] => stdClass Object ( [_] => Pleasure And Pain [Number] => 9 )
    [9] => stdClass Object ( [_] => Walk Away [Number] => 10 )
    [10] => stdClass Object ( [_] => How Many Miles Must We March [Number] => 11 )
    [11] => stdClass Object ( [_] => Welcome To The Cruel World [Number] => 12 )
    [12] => stdClass Object ( [_] => I'll Rise [Number] => 13 )
    [13] => stdClass Object ( [_] => Oppression [Number] => 14 )
    [14] => stdClass Object ( [_] => Ground On Down [Number] => 15 )
    [15] => stdClass Object ( [_] => Another Lonely Day [Number] => 16 )
    [16] => stdClass Object ( [_] => Please Me Like You Want To [Number] => 17 )
    [17] => stdClass Object ( [_] => Gold To Me [Number] => 18 )
    [18] => stdClass Object ( [_] => Burn One Down [Number] => 19 )
    [19] => stdClass Object ( [_] => Excuse Me Mr. [Number] => 20 )
    [20] => stdClass Object ( [_] => People Lead [Number] => 21 )
    [21] => stdClass Object ( [_] => Fight For Your Mind [Number] => 22 )
    [22] => stdClass Object ( [_] => Give A Man A Home [Number] => 23 )
    [23] => stdClass Object ( [_] => By My Side [Number] => 24 )
    [24] => stdClass Object ( [_] => Power Of The Gospel [Number] => 25 )
    [25] => stdClass Object ( [_] => God Fearing Man [Number] => 26 )
    [26] => stdClass Object ( [_] => One Road To Freedom [Number] => 27 )
  )      [Number] => 1 )
)
*/


/* EX00: ONE MEDIA ONLY

[Tracks] => stdClass Object
(
[Disc] => stdClass Object
(
[Track] => Array
(
[0] => stdClass Object
(
[_] => Oppression
[Number] => 1
)

[1] => stdClass Object
(
[_] => Ground On Down
[Number] => 2
)

[2] => stdClass Object
(
[_] => Another Lonely Day
[Number] => 3
)

)

[Number] => 1
)

)
*/



/* EX01 MORE THAN ONE MEDIA

[Tracks] => stdClass Object
(
[Disc] => Array
(
[0] => stdClass Object
(
[Track] => Array
(
[0] => stdClass Object
(
[_] => Fort Worth/Set 2
[Number] => 1
)

[1] => stdClass Object
(
[_] => Birds Of Springtimes Gone By/Set 1
[Number] => 2
)

[2] => stdClass Object
(
[_] => I Can't Get Started/Set 1
[Number] => 3
)

)

[Number] => 1
)

[1] => stdClass Object
(
[Track] => Array
(
[0] => stdClass Object
(
[_] => Lonnie's Lament/Set 2
[Number] => 1
)

[1] => stdClass Object
(
[_] => Reflections/Set 2
[Number] => 2
)

)

[Number] => 2
)

)

)
*/
