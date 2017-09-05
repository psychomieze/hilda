<?php
declare(strict_types=1);

namespace TYPO3\Hilda\Controller;

use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class ReleaseNotes
{

    public function formAction(Request $request, Application $app)
    {
        $form = $this->getForm($app);
        $form->handleRequest($request);

        return $app['twig']->render('ReleaseNotes/Form.twig', ['form' => $form->createView()]);
    }

    public function generateAction(Request $request, Application $app)
    {
        $form = $this->getForm($app);
        $form->handleRequest($request);
        $template = '';
        if ($form->isValid()) {
            $data = $form->getData();
            $releaseNotesData = $app['transformation']->transformFormDataToReleaseNotesData($data);

            if ($data['save_as'] === 'wiki') {
                $template = $app['twig']->render('ReleaseNotes/release-notes-wiki-template.twig', $releaseNotesData);
                $app['media_wiki']->saveReleaseNotesToWiki($template, $releaseNotesData);

                return $app->redirect('https://wiki.typo3.org/TYPO3_CMS_' . $releaseNotesData['full_version']);
            }
            $template = $app['twig']->render('ReleaseNotes/release-notes-template.twig', $releaseNotesData);
        }
        return $template;
    }

    /**
     * @param \Silex\Application $app
     *
     * @return mixed
     */
    protected function getForm(Application $app)
    {
        $form = $app['form.factory']->createNamedBuilder('rn', FormType::class)
            ->setAction('generate')
            ->add('full_version')
            ->add('additional_notes', TextareaType::class)
            ->add('md5_sums', TextareaType::class)
            ->add('changelog', TextareaType::class)
            ->add(
                'date',
                DateType::class,
                [
                    'data' => new \DateTime()
                ]
            )
            ->add(
                'release_type',
                ChoiceType::class,
                [
                    'choices'  => ['minor' => 'minor', 'major' => 'major', 'security' => 'security'],
                    'expanded' => true,
                ]
            )
            ->add(
                'save_as',
                ChoiceType::class,
                [
                    'choices' => ['output' => 'output', 'wiki' => 'wiki']
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Save',
                ]
            )
            ->getForm();
        return $form;
    }
}