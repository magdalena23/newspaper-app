<?php
/**
 * User Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserPasswordType;
use App\Form\UserType;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * User Controller class.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * User Service.
     */
    private UserService $userService;

    /**
     * User Password Encoder Interface.
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserService                  $userService     UserService
     * @param UserPasswordEncoderInterface $passwordEncoder PasswordEncoder
     * @param TranslatorInterface          $translator      TranslatorInterface
     */
    public function __construct(UserService $userService, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
    }

    /**
     * Change password action.
     *
     * @param Request            $request        HTTP request
     * @param User               $user           User
     * @param UserPasswordHasher $passwordHasher UserPasswordHasher
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit_password', name: 'user_edit_password', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function editPassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user, ['method' => 'PUT',
            'action' => $this->generateUrl('user_edit_password', ['id' => $user->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.data_edited_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'user/edit_password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Change email action.
     *
     * @param Request            $request        HTTP request
     * @param User               $user           User
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, ['method' => 'PUT',
            'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.data_edited_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
