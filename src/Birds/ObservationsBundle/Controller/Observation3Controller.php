<?php

namespace Birds\ObservationsBundle\Controller;

use Birds\ObservationsBundle\Entity\Observation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Observation controller.
 *
 */
class ObservationController extends Controller
{
    /**
     * Lists all observation entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $observations = $em->getRepository('BirdsObservationsBundle:Observation')->findAll();

        return $this->render('observation/index.html.twig', array(
            'observations' => $observations,
        ));
    }

    /**
     * Creates a new observation entity.
     *
     */
    public function newAction(Request $request)
    {
        $observation = new Observation();
        $form = $this->createForm('Birds\ObservationsBundle\Form\ObservationType', $observation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($observation);
            $em->flush();

            return $this->redirectToRoute('Obs_show', array('id' => $observation->getId()));
        }

        return $this->render('observation/new.html.twig', array(
            'observation' => $observation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a observation entity.
     *
     */
    public function showAction(Observation $observation)
    {
        $deleteForm = $this->createDeleteForm($observation);

        return $this->render('observation/show.html.twig', array(
            'observation' => $observation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing observation entity.
     *
     */
    public function editAction(Request $request, Observation $observation)
    {
        $deleteForm = $this->createDeleteForm($observation);
        $editForm = $this->createForm('Birds\ObservationsBundle\Form\ObservationType', $observation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Obs_edit', array('id' => $observation->getId()));
        }

        return $this->render('observation/edit.html.twig', array(
            'observation' => $observation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a observation entity.
     *
     */
    public function deleteAction(Request $request, Observation $observation)
    {
        $form = $this->createDeleteForm($observation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($observation);
            $em->flush();
        }

        return $this->redirectToRoute('Obs_index');
    }

    /**
     * Creates a form to delete a observation entity.
     *
     * @param Observation $observation The observation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Observation $observation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('Obs_delete', array('id' => $observation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
