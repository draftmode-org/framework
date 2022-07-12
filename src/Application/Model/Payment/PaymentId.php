<?php
namespace Terrazza\Framework\Application\Model\Payment;

class PaymentId {
    /**
     * @var string
     * @Annotation(minLength=12,maxLength=10)
     */
    private string $id;

    public function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

}