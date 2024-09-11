<?php
// src/Controller/MemoController.php
namespace App\Controller;

use App\Entity\Memo;
use App\Form\MemoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use HTMLPurifier;
use HTMLPurifier_Config;
class MemoController extends AbstractController
{
    #[Route('/memo/new', name: 'app_memo_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $memo = new Memo();
        $form = $this->createForm(MemoType::class, $memo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Clean the content using HTMLPurifier
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $cleanContent = $purifier->purify($memo->getContenu());
            $memo->setContenu($cleanContent);
            $entityManager->persist($memo);
            $entityManager->flush();

            return $this->redirectToRoute('app_memo_list');
        }

        return $this->render('memo/memo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/memo', name: 'app_memo_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $memos = $entityManager->getRepository(Memo::class)->findAll();

        return $this->render('memo/memo_list.html.twig', [
            'memos' => $memos,
        ]);
    }

    #[Route('/memo/edit/{id}', name: 'app_memo_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $memo = $entityManager->getRepository(Memo::class)->find($id);
        if (!$memo) {
            throw $this->createNotFoundException('Le mémo n\'existe pas.');
        }
    
        $form = $this->createForm(MemoType::class, $memo);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $pdfFiles */
            $pdfFiles = $form->get('pdfFiles')->getData();
    
            if ($pdfFiles) {
                foreach ($pdfFiles as $pdfFile) {
                    $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = uniqid().'.'.$pdfFile->guessExtension();
    
                    try {
                        $pdfFile->move(
                            $this->getParameter('pdf_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // handle exception if something happens during file upload
                    }
    
                    $memo->addPdfFilename($newFilename);
                }
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_memo_list');
        }

        return $this->render('memo/memo_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/memo/detail/{id}', name: 'app_memo_detail')]
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $memo = $entityManager->getRepository(Memo::class)->find($id);

        if (!$memo) {
            throw $this->createNotFoundException('Le mémo n\'existe pas.');
        }

        return $this->render('memo/memo_detail.html.twig', [
            'memo' => $memo,
        ]);
    }


    #[Route('/memo/delete/{id}', name: 'app_memo_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, EntityManagerInterface $entityManager): RedirectResponse
        {
            $memo = $entityManager->getRepository(Memo::class)->find($id);

            if (!$memo) {
                throw $this->createNotFoundException('Le mémo n\'existe pas.');
            }

            $entityManager->remove($memo);
            $entityManager->flush();

            return $this->redirectToRoute('app_memo_list');
        }
        }